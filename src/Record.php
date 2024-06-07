<?php 

namespace stm\jmm;

use Exception;

class Record  {
    
    protected $record;
    public function __construct($array)
    {
        $this->record =  $array;  
    }


    /**
     * converting Record instance to array
     * @return array
     */
    public function toArray() : array
    {
        return $this->record;
    }

    /**
     * converting Record instance to JSON
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->record);
    }

    /**
     * check if property exists
     * @param string $property
     */
    public function has(string $property)
    {
        return isset($this->record[$property]);
    }

    /**
     * filtering the record only by the given columns
     * @param array $array
     * @return static
     */
    public function only(array $array) : static
    {
        $record=[];
        foreach($array as $key)
        {
            $record[$key] = $this->record[$key];
        }
        $this->record = $record;
        return $this;
    }

    /**
     * filtering the record columns given
     * @param array $array
     * @return static
     */
    public function except(array $array)
    {
        foreach($array as $key)
        {
            unset($this->record[$key]);
        }
        return $this;
    }

    /**
     * adding columns to record
     * @param array $array
     * @return static
     */
    public function push(array $array) : static
    {
        $this->record = array_merge($this->record,$array);
        return $this;
    }

    /**
     * remove the last colum from the record
     * @return static
     */
    public function pop()
    {
        array_pop($this->record);
        return $this;
    }

    /**
     * adding columns to top of record
     * @param array $array
     * @return static
     */
    public function unshift(array $array) : static
    {
        $this->record = array_merge($array, $this->record);
        return $this;
    }

    /**
     * remove the first column from the record
     * @return static
     */
    public function shift()
    {
        array_shift($this->record);
        return $this;
    }

    /**
     * getting all columns(keys) from the record
     * @return array 
     */
    public function keys() : array
    {
        return array_keys($this->record);
    }

    /**
     * getting all values from the record
     * @return array 
     */
    public function values()
    {
        return array_values($this->record);
    }

    /**
     * getting number of columns of the record
     * @return int 
     */
    public function count() : int
    {
        return count($this->record);
    }

     /**
     * sorting record values
     * @param string $flag
     * @return static
     */
    public function sort($flag = 'asc')
    {
        if($flag == 'asc') asort($this->record, SORT_REGULAR);
        if($flag == 'desc') arsort($this->record, SORT_REGULAR);
        return $this;
    }

    /**
     * sorting records keys
     * @param string $flag
     * @return static
     */
    public function sortKeys($flag = 'asc')
    {
        if($flag == 'asc') ksort($this->record, SORT_REGULAR);
        if($flag == 'desc') krsort($this->record, SORT_REGULAR);
        return $this;
    }






    /**
     * converting array key to a property
     */
    public function __get($name)
    {
        if(!isset($this->record[$name])) return null;
        return $this->record[$name];
    }
    public function __set($name, $value)
    {
        $this->record[$name] = $value;
    }
}