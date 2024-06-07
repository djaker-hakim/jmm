<?php 


namespace stm\jmm;

class Model {


   /**
     * path of the database
     */
    public $path = '.';

    /**
     * database name
     */
    public $db;

    /**
     * the current table 
     */
    public $table;


    
    public $primaryKey = 'id';

    /**
     * auto generation for the primary key
     */
    public $autoGenerate = true;

    /**
     * database columns
     */
    public array $columns = []; 



    public static function __callStatic($name, $arguments)
    {
        $MODEL = new static();
        $DB = new Database($MODEL->path);
        return $DB->db($MODEL->db)->table($MODEL->table)->$name(...$arguments);        
    }

    public function __call($name, $arguments)
    {
        $DB = new Database($this->path);
        return $DB->db($this->db)->table($this->table)->$name(...$arguments);
    }



}