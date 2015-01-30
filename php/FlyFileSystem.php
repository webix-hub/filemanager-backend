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

class FlyFileInfo extends RealFileInfo{
    protected $aFilesystem;

    function __construct($path, Filesystem $aFilesystem){
        $this->aFilesystem = $aFilesystem;

        $file = $this->aFilesystem->getMetadata($path);

        $this->content = $this->aFilesystem->read($path);
        $this->name = basename($file['path']);
        $this->ext = end(explode('.', $file['path']));
        $this->myme = $this->aFilesystem->getMimetype($path);
    }
}

class FlyFileSystem extends CommandFileSystem{
    protected $aFilesystem;

    function __construct(Filesystem $aFilesystem, $topdir = "/", $topurl = "/") {
        parent::__construct($topdir, $topurl);
        $this->aFilesystem = $aFilesystem;
    }

    protected function dir($dir, $nested){
        $data = array();

        $files = $this->aFilesystem->listContents($dir, false);
        foreach ($files as $file){
            if ($file["basename"] != "." && $file["basename"] != "..") {
                $isdir = $file['type'] == 'dir';
                $temp = array(
                    "id" => $this->file_id($file["path"]),
                    "value" => $file["basename"],
                    "type" => $isdir ? 'folder' : $this->get_type($file["basename"]),
                    "size" => $isdir ? 0 : $file['size'],
                    "date" => array_key_exists('timestamp', $file) ? $file['timestamp'] : ''
                );

                if ($isdir && $nested){
                    $temp["data"] = $this->dir($file["path"], $nested);
                }

                $data[] = $temp;
            }
        }

        usort($data, array($this, "sort"));

        return $data;
    }

    public function download($file){
        return new FlyFileInfo($file, $this->aFilesystem);
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

    protected function ren($source, $target, $name){
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
                    $this->ren($file["path"], $target.$this->sep.$file["basename"], $file["basename"]);
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

    protected function ren($source, $target, $name){
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