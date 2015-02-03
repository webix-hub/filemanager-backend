<?php

require_once("AbstractFileSystem.php");

class PHPFileSystem extends AbstractFileSystem{
    protected function unlink($path){
        if ($this->debug || $this->test){
            $this->log("Delete $path");
        }

        if (!$this->test){
            if (is_file($path)){
                unlink($path);
            } else {
                $files = array_diff(scandir($path), array('.','..'));
                foreach ($files as $file) {
                    $this->unlink($path.$this->sep.$file);
                }
                rmdir($path);
            }
        }
    }

    protected function makedir($target){
        if ($this->debug || $this->test){
            $this->log("Makedir $target");
        }

        if (!$this->test){
            mkdir($target);
        }
    }

    protected function ren($source, $target, $name){
        if ($this->debug || $this->test){
            $this->log("Rename ($source | $target | $name)");
        }

        if (!$this->test){
            if (is_file($source)){
                rename($source, $target);
            } else {
                if(!file_exists($target)){
                    rename($source, $target);
                } else {
                    $files = scandir($source);
                    foreach ($files as $file){
                        if ($file != "." && $file != "..") {
                            $this->ren($source.$this->sep.$file, $target.$this->sep.$file, $file);
                        }
                    }
                    rmdir($source);
                }
            }
        }
    }

    protected function move($source, $target){
        if ($this->debug || $this->test){
            $this->log("Move ($source | $target)");
        }

        if (!$this->test){
            if (is_file($source)){
                rename($source, $target.$this->sep.basename($source));
            } else {
                if(!file_exists($target.$this->sep.basename($source))){
                    rename($source, $target.$this->sep.basename($source));
                } else {
                    $files = scandir($source);
                    foreach ($files as $file){
                        if ($file != "." && $file != "..") {
                            $this->move($source.$this->sep.$file, $target.$this->sep.basename($source));
                        }
                    }
                    rmdir($source);
                }
            }
        }
    }

    protected function copy($source, $target){
        if ($this->debug || $this->test){
            $this->log("Copy ($source | $target)");
        }

        if (!$this->test){
            $dst = $target.$this->sep.basename($source);
            if (is_file($source)){
                copy($source, $dst);
            } else {
                if(!file_exists($dst)){
                    mkdir($dst);
                }
                $files = scandir($source);
                foreach ($files as $file){
                    if ($file != "." && $file != "..") {
                        $this->copy($source.$this->sep.$file, $dst);
                    }
                }
            }
        }
    }
}

?>