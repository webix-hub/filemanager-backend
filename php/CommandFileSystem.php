<?php
require_once("FileSystem.php");

class CommandFileSystem implements iFileSystem{
	public  $debug = false;
	public $batchSeparator = ",";
	public $extensions = array(
		"docx" 	=> "doc",
		"xsl" 	=> "excel",
		"xslx" 	=> "excel",
		"txt"	=> "text", "md"=>"text",
		"html"	=> "code", "js"=>"code", "json"=>"code", "css"=>"code", "php"=>"code", "htm"=>"code",
		"mpg"	=> "video", "mp4"=>"video","avi"=>"video","mkv"=>"video",
		"png"	=> "image", "jpg"=>"image", "gif"=>"image",
		"mp3"	=> "audio", "ogg"=>"audio",
		"zip"	=> "archive", "rar"=>"archive", "7z"=>"archive", "tar"=>"archive", "gz"=>"archive"
	);

    protected $top;
    protected $url;
    protected $win;
	protected $sep;
    protected $vroot = false;

	function __construct($topdir = "/", $topurl = "/"){
		$this->top = realpath($topdir);
		$this->url = $topurl;
		$this->win = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
		$this->sep = $this->win ? "\\" : "/";

		if (substr($this->top, -1) != $this->sep)
			$this->top .= $this->sep;
	}

	function virtualRoot($name){
		$this->vroot = $name;
	}

    protected function get_type($entry){
		$ext = pathinfo($entry, PATHINFO_EXTENSION);
		if ($ext && isset($this->extensions[$ext]))
			return $this->extensions[$ext];
		return $ext;
	}

	private function top_dir($source){
		$data = explode($this->sep, $source);
		return $data[sizeof($data)-1];
	}

	protected function file_id($full){
 		return str_replace($this->top, "", $full);
	}

	private function safe_name($name){
		$name = str_replace("..","",preg_replace("|[^a-z0-9-_\\.\\/\\:]|i", "", str_replace("\\","/",$name)));
		if ($this->win)
			$name = str_replace("/", "\\", $name);
		else
			$name = str_replace("\\", "/", $name);

		return preg_replace('#[\\\\\\/]+#', $this->sep, $name);
	}
	private function check_path($path, $folder = false, $file = false){
		$path = $this->safe_name($this->top.$path);
		
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

    protected function dir($dir, $nested){
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
				$temp["data"] = $this->dir($temp["id"], $nested);
			}

			$data[] = $temp;
		}
		$d->close();

		usort($data, array($this, "sort"));

		return $data;
	}

	public function ls($dir, $nested = false){
		$data = $this->dir($dir, $nested);
		if ($this->vroot)
			return array(
				array( 
					"value" => $this->vroot,
					"type" => "folder",
					"size" => 0,
					"date" => 0,
					"id" => "/",
					"data" => &$data,
					"open" => true
				)
			);
		
		return $data;
	}

	public function sort($a, $b){
		$af = $a["type"] == "folder";
		$bf = $b["type"] == "folder";
		if ($af && !$bf) return -1;
		if ($bf && !$af) return 1;

		return $a["value"] > $b["value"] ? 1 : ($a["value"] < $b["value"] ? -1 : 0);
	}

	public function batch($source, $operation, $target = null){
		if (!is_array($source))
			$source = explode($this->batchSeparator, $source);

		$result = array();
		for ($i=0; $i < sizeof($source); $i++)
			if ($target !== null)
				$result[] = call_user_func($operation, $source[$i], $target);
			else
				$result[] = call_user_func($operation, $source[$i]);
		
		return $result;
	}

	public function rm($file){
		$file = $this->check_path($file, true, true);

		//do not allow root deletion
		if ($this->file_id($file) !== "")
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

	public function mkdir($name, $path){
		$path = $this->check_path($path.$this->sep.$name);
		$this->makedir($path);
		$id = str_replace("\\","/",str_replace($this->top, "", $path));
		return array( "id" => $id );
	}

	public function rename($source, $target){
		$name = $this->safe_name($target);
		$target = $this->check_path(dirname($source).$this->sep.$target);
		$source = $this->check_path($source, true, true);


		$this->ren($source, $target, $name);
		$id = str_replace("\\","/",str_replace($this->top, "", $target));
		return array( "id" => $id );
	}

	public function cat($path){
		$path = $this->check_path($path, false, true);

		return file_get_contents($path);
	}

	public function upload($path, $name, $temp){
				$this->check_path($path, true, false);
		$full = $this->check_path($path.$this->sep.$name);
		
		move_uploaded_file($temp, $full);
		return array(
			"folder" => $this->safe_name($path),
			"file"   => $this->safe_name($name),
			"id"     => $this->file_id($full),
			"type"   => $this->get_type($name),
			"status" => "server"
		);
	}

	public function download($file){
		$file = $this->check_path($file, false, true);
		return new RealFileInfo($file);
	}

	public function url($path){
		$path = $this->check_path($path, false, true);

		return $this->url.$path;
	}
}
?>