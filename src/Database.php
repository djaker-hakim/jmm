<?php 

namespace stm\jmm;

use Exception;

class Database extends Collection {

    /**
     * path of the database
     */
    protected $path = '.';

    /**
     * database name
     */
    protected $name;

    /**
     * the current table 
     */
    protected $table;


    
    public $primaryKey = 'id';

    /**
     * auto generation for the primary key
     */
    public $autoGenerate = true;

    /**
     * database columns
     */
    public array $columns = []; 

    
    public function __construct()
    {
        
    }

    /**
     * setting up the path of the table
     */
    protected function fullPath()
    {
        return $this->path.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR.$this->table.'.json';
    }

    /**
     * setter for the database name
     * @param string $name
     * @return static
     */
    public function db(string $name) : static
    {
        $this->name = $name;
        return $this;
    }


    /**
     * setter for the table
     * @param string $name
     * @return static
     */
    public function table(string $name) : static
    {
        $this->table = $name;
        return $this->getRecords();
    }

       
    /**
     * getting the data from the table
     * @return static
     */
    protected function getRecords() : static
    {
        $array = json_decode(file_get_contents($this->fullPath()), true);
        if($array)
        {
            $this->collect($array);
        }else{
            $this->allRecords = [];
            $this->reset();
        }  
        return $this;
    }

    /**
     * saving data to the table
     * @return static
     */
    protected function saveRecords() : static
    {    
        file_put_contents($this->fullPath(), $this->toJson());
        return $this->getRecords();
    }

    
    /**
     * generating uuid
     * @return string
     */
    public function uuid() : string
    {
        return uniqid();
    }

    /**
     * generating a unique primary key
     * @return array
     */
    public function generatePrimaryKey() : array
    {
        return [$this->primaryKey => $this->uuid()];
    }

    /**
     * validating if the array added contains the required columns
     * @param array $array
     * @return void 
     */
    public function columnsValidation(array $array) : void
    {
        if(!$this->columns) return;
        foreach($this->columns as $col)
        {
            if(!array_key_exists($col, $array)) throw new Exception("Column validation failed: the column '$col' is missing.");
        }
    }


    /**
     * adding a record to the database
     * @param array $array
     * @return Record
     */
    public function create(array $array) : Record
    {
        $this->columnsValidation($array);

        if($this->autoGenerate){
            if(array_key_exists($this->primaryKey, $array)) throw new Exception("autogeneration is enabled");            
            $record = array_merge($this->generatePrimaryKey(), $array);                  
        }else{
            if(!array_key_exists($this->primaryKey, $array)) throw new Exception("A primary key needs to be added.");
            $record = $array;
        }
        $this->push($record)->saveRecords();
        return new Record($record);
    }


    /**
     * delete record based on the IDs given or at a quary
     * @param array|string|int $ids
     * @return array|Record
     */
    public function delete(array|string|int $ids='') : array|Record
    {
        if(!$ids)
        {
            $records = $this->records;
            $this->destroy()->saveRecords();
            return $records;
        }

        if(is_array($ids)){
            $records= [];
            foreach($ids as $id){
                $records[] = $this->where($this->primaryKey, $id)->first();
                $this->where($this->primaryKey, $id)->destroy();
                $this->reset(); 
            }
            $this->saveRecords();
            return $records;
        }
        $record = $this->where($this->primaryKey, $ids)->first();
        $this->where($this->primaryKey, $ids)->destroy()->saveRecords();
        return $record;
    }



    /**
     * update the record based on query
     * @param array $array
     * @return array
     */
    public function update(array $array) : array
    {
        $updateRec = [];
        foreach($this->records as $record)
        {
            foreach($array as $key => $value)
            {
                $record->$key = $value;
            }
            $updateRec[] = $record;
        }
        $this->destroy();
        foreach($updateRec as $rec)
        {
            $this->push($rec->toArray());
        }
        $this->saveRecords();
        return $updateRec;
    }






}