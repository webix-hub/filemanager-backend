<?php
interface iDBFileSystem {
    public function virtualRoot($id);
    public function ls($id, $nested);
    public function rm($id, $table);
    public function cp($id, $folder_id, $type);
    public function mv($id, $folder_id, $type);
    public function mkdir($name, $folder_id);
    public function rename($id, $name, $type);

    public function batch($source, $operation);
}

interface iTableStructure{
    public function getId();
    public function getValue();
    public function getFolderId();
    public function getTableName();
    public function getType();
    public function getDataFields();
}

class RealTableStructure implements iTableStructure{
    protected $id;
    protected $value;
    protected $folderId;
    protected $tableName;
    protected $type;
    protected $data_fields = array();

    function __construct($config){
        $this->id = isset($config['structure']['id']) ? $config['structure']['id'] : 'id';
        $this->value = isset($config['structure']['value']) ? $config['structure']['value'] : 'value';
        $this->folderId = isset($config['structure']['folder_id']) ? $config['structure']['folder_id'] : 'folder_id';
        $this->tableName = $config['table_name'];
        $this->type = $config['type'];

        if(isset($config['structure']['data_fields'])){
            $this->data_fields = is_array($config['structure']['data_fields']) ? $config['structure']['data_fields'] : explode(',', preg_replace("/\s/", "", $config['structure']['data_fields']));
        }
    }

    public function getId(){
        return $this->id;
    }
    public function getValue(){
        return $this->value;
    }
    public function getFolderId(){
        return $this->folderId;
    }
    public function getTableName(){
        return $this->tableName;
    }
    public function getType(){
        return $this->type;
    }
    public function getDataFields(){
        return $this->data_fields;
    }
}


class PDOConfig extends PDO {
    protected $engine;
    protected $host;
    protected $database;
    protected $user;
    protected $pass;

    public function __construct($config){
        $this->engine = isset($config['engine'])?$config['engine']:'';
        $this->host = isset($config['host'])?$config['host']:'';
        $this->database = isset($config['database'])?$config['database']:'';
        $this->user = isset($config['user'])?$config['user']:'';
        $this->pass = isset($config['pass'])?$config['pass']:'';

        //$dns = $this->engine.':dbname='.$this->database.";host=".$this->host; //for mysql

        $dns = $this->engine.':'.$this->database;
        parent::__construct( $dns, $this->user, $this->pass );
    }
}

class DBFileSystem implements iDBFileSystem{
    public $debug = false;
    public $test  = false;
    private $db;
    private $vrootId = false;
    private $extensions = array(
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

    private $folders;
    private $files;

    public function __construct($config, $config_folders, $config_files){
        $this->db = new PDOConfig($config);
        // Set errormode to exceptions
        $this->db->setAttribute(PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION);

        $this->folders = new RealTableStructure($config_folders);
        $this->files = new RealTableStructure($config_files);
    }

    function virtualRoot($id){
        $this->vrootId = $id;
    }

    protected function get_type($entry){
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext && isset($this->extensions[$ext]))
            return $this->extensions[$ext];
        return $ext;
    }

    protected function log($message){
        echo $message."\n";
    }

    protected function dir($folder_id, $nested){
        $data = array();
        if ($this->debug || $this->test){
            $this->log("List from $folder_id");
        }

        $folders = $this->db->prepare("SELECT * FROM ".$this->folders->getTableName()." WHERE ".$this->folders->getFolderId()." = :folder_id");
        $folders->bindParam(':folder_id', $folder_id);
        $folders->execute();

        $files = $this->db->prepare("SELECT * FROM ".$this->files->getTableName()." WHERE ".$this->files->getFolderId()." = :folder_id");
        $files->bindParam(':folder_id', $folder_id);
        $files->execute();

        foreach($folders as $row) {
            $temp = array(
                "id" => $row[$this->folders->getId()],
                "value" => $row[$this->folders->getValue()],
                "type" => "folder"
            );

            foreach($this->folders->getDataFields() as $field) {
                $temp[$field] = $row[$field];
            }

            if ($nested){
                $temp["data"] = $this->dir($temp["id"], $nested);
            }

            $data[] = $temp;
        }

        foreach($files as $row) {
            $temp = array(
                "id" => $row[$this->files->getId()],
                "value" => $row[$this->files->getValue()],
                "type" => $this->get_type($row[$this->files->getValue()])
            );

            foreach($this->files->getDataFields() as $field) {
                $temp[$field] = $row[$field];
            }

            $data[] = $temp;
        }

        usort($data, array($this, "sort"));

        return $data;
    }

    public function ls($id, $nested = false){
        $data = $this->dir($id, $nested);

        if ($this->vrootId){
            $root_folder = $this->db->prepare("SELECT * FROM ".$this->folders->getTableName()." WHERE ".$this->folders->getId()." = :id");
            $root_folder->bindParam(':id', $this->vrootId);
            $root_folder->execute();
            $root_folder = $root_folder->fetch();

            return array(
                array(
                    "value" => $root_folder[$this->folders->getValue()],
                    "type" => "folder",
                    "id" => $this->vrootId,
                    "data" => &$data,
                    "open" => true
                )
            );
        }

        return $data;
    }

    public function sort($a, $b){
        $af = $a["type"] == "folder";
        $bf = $b["type"] == "folder";
        if ($af && !$bf) return -1;
        if ($bf && !$af) return 1;

        return $a["value"] > $b["value"] ? 1 : ($a["value"] < $b["value"] ? -1 : 0);
    }

    public function batch($source, $operation){
        if (!is_array($source))
            $source = explode(',', $source);

        $result = array();
        for ($i = 0; $i < sizeof($source); $i++){
            if (!is_array($source[$i]))
                $source[$i] = explode(',', $source[$i]);
            $result[] = call_user_func_array($operation, $source[$i]);
        }
        return $result;
    }

    public function rm($id, $type){
        $table = $type == 'folder' ? $this->folders : $this->files;

        $this->unlink($id, $table);
        return "ok";
    }

    public function cp($id, $folder_id, $type){
        $table = $type == 'folder' ? $this->folders : $this->files;

        $this->copy($id, $folder_id, $table);
        return "ok";
    }

    public function mv($id, $folder_id, $type){
        $table = $type == 'folder' ? $this->folders : $this->files;

        $this->move($id, $folder_id, $table);
        return "ok";
    }

    public function mkdir($name, $folder_id){
        $id = $this->makedir($name, $folder_id);

        return array( "id" => $id );
    }

    public function rename($id, $name, $type){
        $table = $type == 'folder' ? $this->folders : $this->files;

        $this->ren($id, $name, $table);

        return array( "id" => $id );
    }

    protected function unlink($id, RealTableStructure $table){
        if ($this->debug || $this->test){
            $this->log("Delete $id from ".$table->getType());
        }

        if (!$this->test){
            if($table->getType() == 'folders'){
                $folders = $this->db->prepare("SELECT * FROM ".$this->folders->getTableName()." WHERE ".$this->folders->getFolderId()." = :folder_id");
                $folders->bindParam(':folder_id', $id);
                $folders->execute();

                $files = $this->db->prepare("SELECT * FROM ".$this->files->getTableName()." WHERE ".$this->files->getFolderId()." = :folder_id");
                $files->bindParam(':folder_id', $id);
                $files->execute();

                foreach($folders as $row) {
                    $this->unlink($row[$this->folders->getId()], $this->folders);
                }

                foreach($files as $row) {
                    $this->unlink($row[$this->files->getId()], $this->files);
                }
            }
            $sql = "DELETE FROM ".$table->getTableName()." WHERE ".$table->getId()." = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    }

    protected function makedir($name, $folder_id){
        if ($this->debug || $this->test){
            $this->log("Makedir $name in folder_id=$folder_id");
        }

        if (!$this->test){
            $stmt = $this->db->prepare("INSERT INTO ".$this->folders->getTableName()." (".$this->folders->getValue().", ".$this->folders->getFolderId().") VALUES (:value, :folder_id)");
            $stmt->bindParam(':value', $name);
            $stmt->bindParam(':folder_id', $folder_id);
            $stmt->execute();

            return $this->db->lastInsertId();
        }
    }

    protected function ren($id, $name, RealTableStructure $table){
        if ($this->debug || $this->test){
            $this->log("Rename $id to $name from ".$table->getType());
        }

        if (!$this->test){
            $sql = "UPDATE ".$table->getTableName()." SET ".$table->getValue()." = :value WHERE ".$table->getId()." = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':value', $name);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    }

    protected function move($id, $folder_id, RealTableStructure $table){
        if ($this->debug || $this->test){
            $this->log("Move $id to $folder_id from ".$table->getType());
        }

        if (!$this->test){
            $sql = "UPDATE ".$table->getTableName()." SET ".$table->getFolderId()." = :folder_id WHERE ".$table->getId()." = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':folder_id', $folder_id);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    }

    protected function copy($id, $folder_id, RealTableStructure $table){
        if ($this->debug || $this->test){
            $this->log("Copy $id to $folder_id from ".$table->getType());
        }

        if (!$this->test){
            $cf = $this->db->prepare("SELECT * FROM ".$table->getTableName()." WHERE ".$table->getId()." = :id");
            $cf->bindParam(':id', $id);
            $cf->execute();
            $cf = $cf->fetch();

            $sql = "INSERT INTO ".$table->getTableName()." SET ".$table->getFolderId()." = :folder_id, ".$table->getValue()." = :value";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':folder_id', $folder_id);
            $stmt->bindParam(':value', $cf[$table->getValue()]);
            $stmt->execute();

            if($table->getType() == 'folders'){
                $new_id = $this->db->lastInsertId();

                $folders = $this->db->prepare("SELECT * FROM ".$this->folders->getTableName()." WHERE ".$this->folders->getFolderId()." = :folder_id");
                $folders->bindParam(':folder_id', $id);
                $folders->execute();

                $files = $this->db->prepare("SELECT * FROM ".$this->files->getTableName()." WHERE ".$this->files->getFolderId()." = :folder_id");
                $files->bindParam(':folder_id', $id);
                $files->execute();

                foreach($folders as $row) {
                    $this->copy($row[$this->folders->getId()], $new_id, $this->folders);
                }

                foreach($files as $row) {
                    $this->copy($row[$this->files->getId()], $new_id, $this->files);
                }
            }
        }
    }
}