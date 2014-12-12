<?php
interface iFileSystem {
	public function ls($dir, $nested);
	public function rm($file);
	public function cp($source, $target);
	public function mv($source, $target);

	public function touch($path);
	public function cat($path);

	public function upload($path, $temp);
	public function url($path);
}

class RealFileSystem implements iFileSystem{
	public  $debug = false;
	public $extensions = array(
		"docx" 	=> "doc",
		"xsl" 	=> "excel",
		"xslx" 	=> "excel",
		"txt"	=> "text", "md"=>"text",
		"html"	=> "code", "js"=>"code", "css"=>"code", "php"=>"code", "htm"=>"code",
		"mpg"	=> "video", "mp4"=>"video","avi"=>"video","mkv"=>"video",
		"png"	=> "image", "jpg"=>"image", "gif"=>"image",
		"mp3"	=> "audio", "ogg"=>"audio",
		"zip"	=> "archive", "rar"=>"archive", "7z"=>"archive", "tar"=>"archive", "gz"=>"archive"
	);

	private $top;
	private $url;
	private $win;
	private $sep;	

	function __construct($topdir = "/", $topurl = "/"){
		$this->top = realpath($topdir);
		$this->url = $topurl;
		$this->win = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
		$this->sep = $this->win ? "\\" : "/";

		if (substr($this->top, -1) != $this->sep)
			$this->top .= $this->sep;
	}

	private function get_type($entry){
		$ext = pathinfo($entry, PATHINFO_EXTENSION);
		if ($ext && isset($this->extensions[$ext]))
			return $this->extensions[$ext];
		return $ext;
	}

	private function check_path($path, $folder = false, $file = false){
		$path = str_replace("..","",preg_replace("|[^a-z0-9-_\\.\\/\\:]|i", "", str_replace("\\","/",$this->top.$path)));
		if ($this->win)
			$path = str_replace("/", "\\", $path);
		else
			$path = str_replace("\\", "/", $path);

		$path = preg_replace('#[\\\\\\/]+#', $this->sep, $path);



		if (!$path || strpos($path, $this->top) !== 0)
			throw new Exception("Path is outside of sandbox: ".$path);

		if ($folder && $file){
			if (!file_exists($path))
				throw new Exception("Path is invalid: ".$path);
		}
		else {
			if ($folder){
				if (!is_dir($path))
					throw new Exception("Path is not a Directory: ".$path);
				else
					if (substr($path, -1) != $this->sep)
						$path .= $this->sep;
			}

			if ($file && !is_file($path))
				throw new Exception("Path is not a File : ".$path);
		}
		return $path;
	}

	private function exec($command){
		if ($this->debug)
			echo $command."\n";
		else
			exec($command);
	}
	private function log($message){
		if ($this->debug)
			echo $message."\n";
	}

	private function unlink($path){
		if ($win){
			if (is_file($path))
				$this->exec("del /s $path");
			else
				$this->exec("rd /s /q $path");
		}
		else
			$this->exec("rm -rf $path");
	}
	private function move($source, $target){
		if ($win){
			$this->exec("robocopy $source $target /e /move");
		}
		else
			$this->exec("mv -rf $source $target");
	}
	private function copy($source, $target){
		if ($win){
			$this->exec("robocopy $source $target /e");
		}
		else
			$this->exec("cp -rf $source $target");
	}

	public function ls($dir, $nested = false){
		$dir = $this->check_path($dir, true);
		$this->log("List $dir");

		$data = array();
		$d = dir($dir);
		$folder = str_replace("\\","/",str_replace($this->top, "", $dir));

		while(false != ($entry = $d->read())){
			if ($entry == "." || $entry == "..") continue;

			$file = $d->path.$entry;
			$isdir = is_dir($file);
			$temp = array(
				"id" => $folder.$entry,
				"value" => $entry,
				"type" => $isdir ? "folder" : $this->get_type($entry),
				"size" => $isdir ? 0 : filesize($file),
				"date" => filemtime($file)
			);

			if ($isdir && $nested){
				$temp["data"] = $this->ls($temp["id"], $nested);
			}

			$data[] = $temp;
		}
		$d->close();

		usort($data, array($this, "sort"));

		return $data;
	}
	public function sort($a, $b){
		$af = $a["type"] == "folder";
		$bf = $b["type"] == "folder";
		if ($af && !$bf) return -1;
		if ($bf && !$af) return 1;

		return $a["value"] > $b["value"] ? 1 : ($a["value"] < $b["value"] ? -1 : 0);
	}

	public function rm($file){
		$file = $this->check_path($file, true, true);

		$this->unlink($file);
		return "ok";
	}

	public function cp($source, $target){
		$source = $this->check_path($source, true, true);
		$target = $this->check_path($target);

		$this->copy($source, $target);
		return "ok";
	}

	public function mv($source, $target){
		$source = $this->check_path($source, true, true);
		$target = $this->check_path($target);

		$this->move($source, $target);
		return "ok";
	}

	public function touch($path, $content = ""){
		$path = $this->check_path($path);

		file_put_contents($path, $content);
		return "ok";
	}

	public function mkdir($path){
		$path = $this->check_path($path);
		throw new Exception("Not implemented");
	}

	public function cat($path){
		$path = $this->check_path($path, false, true);

		return file_get_contents($path);
	}

	public function upload($path, $temp){
		$path = $this->check_path($path, false, true);
		throw new Exception("Not implemented");

		return "ok";
	}

	public function url($path){
		$path = $this->check_path($path, false, true);

		return $this->url.$path;
	}
}