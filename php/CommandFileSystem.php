<?php
require_once("AbstractFileSystem.php");


class CommandFileSystem extends AbstractFileSystem{
	private function exec($command){
		if ($this->debug || $this->test)
			$this->log($command);
		if (!$this->test)
			exec($command);
	}

    protected function unlink($path){
		if ($this->win){
			if (is_file($path))
				$this->exec("del /s $path");
			else
				$this->exec("rd /s /q $path");
		}
		else
			$this->exec("rm -rf $path");

	}

    protected function makedir($target){
		$this->exec("mkdir $target");
	}

	protected function ren($source, $target, $name){
		if ($this->win)
			$this->exec("rename $source $name");
		else
			$this->exec("mv -rf $source $target");
	}

    protected function move($source, $target){
		if ($this->win){
			if (is_file($source))
				$this->exec("move $source $target");
			else
				$this->exec("robocopy $source ".$this->safe_name($target.$this->sep.$this->top_dir($source))." /e /move");
		}
		else
			$this->exec("mv -rf $source $target");
	}

    protected function copy($source, $target){
		if ($this->win){
			if (is_file($source))
				$this->exec("copy $source $target");
			else
				$this->exec("robocopy $source ".$this->safe_name($target.$this->sep.$this->top_dir($source))." /e");
		}
		else
			$this->exec("cp -rf $source $target");
	}
}
?>