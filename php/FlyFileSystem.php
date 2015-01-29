<?php
require_once("CommandFileSystem.php");

spl_autoload_register(function($class) {
    $prefix = 'League\\Flysystem\\';

    if ( ! substr($class, 0, 17) === $prefix) {
        return;
    }

    $class = substr($class, strlen($prefix));
    $location = __DIR__ . '/flysystem/src/' . str_replace('\\', '/', $class) . '.php';

    if (is_file($location)) {
        require_once($location);
    }
});

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;


class FlyFileSystem extends CommandFileSystem{
    protected $aFilesystem;

    function __construct(Filesystem $aFilesystem) {
        $this->aFilesystem = $aFilesystem;
    }
}


class ZipFlyFileSystem extends FlyFileSystem{
    protected function unlink($path){
        $path = $this->file_id($path);

        if ($this->aFilesystem->has($path)){
            return $this->aFilesystem->delete($path);
        } else {
            return $this->aFilesystem->deleteDir($path);
        }
    }

    protected function makedir($target){
        $target = $this->file_id($target);

        return $this->aFilesystem->createDir($target);
    }

    protected function ren($source, $target){
        $source = $this->file_id($source);
        $target = $this->file_id($target);

        if ($this->aFilesystem->has($source)){
            $this->aFilesystem->rename($source, $target);
        } else {
            $is_empty = $this->aFilesystem->listContents($target, true);
            if(empty($is_empty)){
                $this->aFilesystem->createDir($target);
            }

            $files = $this->aFilesystem->listContents($source, false);
            foreach ($files as $file){
                if ($file["basename"] != "." && $file["basename"] != "..") {
                    $this->ren($file["path"], $target.$this->sep.$file["basename"]);
                }
            }

            $this->aFilesystem->deleteDir($source);
        }
    }

    protected function move($source, $target){
        $source = $this->file_id($source);
        $target = $this->file_id($target);

        $dst = $target.$this->sep.basename($source);

        if ($this->aFilesystem->has($source)){
            $this->aFilesystem->rename($source, $dst);
        } else {
            $is_empty = $this->aFilesystem->listContents($dst, true);
            if(empty($is_empty)){
                $this->aFilesystem->createDir($dst);
            }

            $files = $this->aFilesystem->listContents($source, false);
            foreach ($files as $file){
                if ($file["basename"] != "." && $file["basename"] != "..") {
                    $this->move($file["path"], $dst);
                }
            }
            $this->aFilesystem->deleteDir($source);
        }
    }

    protected function copy($source, $target){
        $source = $this->file_id($source);
        $target = $this->file_id($target);

        $dst = $target.$this->sep.basename($source);

        if ($this->aFilesystem->has($source)){
            $this->aFilesystem->copy($source, $dst);
        } else {
            $is_empty = $this->aFilesystem->listContents($dst, true);
            if(empty($is_empty)){
                $this->aFilesystem->createDir($dst);
            }

            $files = $this->aFilesystem->listContents($source, false);
            foreach ($files as $file){
                if ($file["basename"] != "." && $file["basename"] != "..") {
                    $this->copy($file["path"], $dst);
                }
            }
        }
    }
}

class LocalFlyFileSystem extends FlyFileSystem{
    protected function unlink($path){
        $path = $this->file_id($path);

        if ($this->aFilesystem->getMetadata($path)['type'] == 'file'){
            return $this->aFilesystem->delete($path);
        } else {
            return $this->aFilesystem->deleteDir($path);
        }
    }

    protected function makedir($target){
        $target = $this->file_id($target);

        $this->aFilesystem->createDir($target);
    }

    protected function ren($source, $target){
        $source = $this->file_id($source);
        $target = $this->file_id($target);

        $this->aFilesystem->rename($source, $target);
    }

    protected function move($source, $target){
        $source = $this->file_id($source);
        $target = $this->file_id($target);

        $dst = $target.$this->sep.basename($source);

        $this->aFilesystem->rename($source, $dst);
    }

    protected function copy($source, $target){
        $source = $this->file_id($source);
        $target = $this->file_id($target);

        $dst = $target.$this->sep.basename($source);

        if ($this->aFilesystem->getMetadata($source)['type'] == 'file'){
            $this->aFilesystem->copy($source, $dst);
        } else {
            if (!$this->aFilesystem->has($source)){
                $this->aFilesystem->createDir($dst);
            }

            $files = $this->aFilesystem->listContents($source, false);
            foreach ($files as $file){
                if ($file["basename"] != "." && $file["basename"] != "..") {
                    $this->copy($file["path"], $dst);
                }
            }
        }
    }
}