<?php 

namespace stm\jmm;

use Exception;

class Schema {


    /**
     * path of the database
     */
    protected $path;

    /**
     * database name
     */
    protected $databaseName;

    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    /**
     * setter for the path
     * @param string $path path of database
     * @return void
     */
    public function setPath(string $path) : void
    {
        $this->path = $path;
    }

    /**
     * generating the path of database 
     * @return string
     */
    protected function fullPath() : string
    {
        return $this->path.DIRECTORY_SEPARATOR.$this->databaseName;
    }

    /**
     * validate the name of the database or/and table
     * @param string $name
     * @return void
     */
    protected function nameValidation(string $name) : void
    {
        $forbidden = "/*.,:;!%'{([|`)]} ";
        for($i=0; $i < strlen($forbidden); $i++)
        {
            if(!(strpos($name, $forbidden[$i]) === false)) throw new Exception("The character '$forbidden[$i]' is not permitted.");
        }
    }


    /**
     * setter of database name
     * @param string $name
     * @return static 
     */
    public function db(string $name) : static
    {
        $this->nameValidation($name);
        $this->databaseName = $name;
        return $this;
    }


    /**
     * create a database
     * @param string $name
     * @return bool
     */
    public function createDB(string $name) : bool
    {
        $this->db($name);
        return mkdir($this->path.DIRECTORY_SEPARATOR.$this->databaseName);
    }

    /**
     * drop a database
     * @param string $name
     * @return bool
     */
    public function dropDB(string $name) : bool
    {
        $this->db($name);
        $dirname = $this->path.DIRECTORY_SEPARATOR.$this->databaseName;
        array_map('unlink', glob("$dirname/*.*"));
        return rmdir($dirname);
    }


    /**
     * create a table
     * @param string $name
     * @return int|false
     */
    public function createTable(string $name) : int|false
    {
        $filename = $this->fullPath().DIRECTORY_SEPARATOR.$name.'.json';
        return file_put_contents($filename, []);
    }

    /**
     * drop a table
     * @param string $name
     * @return bool
     */
    public function dropTable(string $name) : bool
    {
        $filename = $this->fullPath().DIRECTORY_SEPARATOR.$name.'.json';
        return unlink($filename);
    }
}