<?php

namespace stm\jmm;



class Collection {

    /**
     * query based records
     */
    protected array $records;
    

    protected array $allRecords;

    public function __construct(array $array)
    {
      $this->collect($array);
    }


    /**
     * init the collection by converting the array elements to a Record instance
     * @param array $array
     * @return void
     */
    public function collect(array $array) : void
    {
        foreach($array as $record)
        {
            $this->allRecords[] = $this->record($record);
        }
        $this->reset();
    }



    // TOOLS FUNCTIONS

    /**
     * converte array to a Record instance
     * @param mixed $array
     * @return mixed
     */
    protected function record($array)
    {
        if(!is_array($array)) return $array;
        if(array_is_list($array)) return $array;
        return new Record($array);
    }

    /**
     * tests if the record is an enstance of Record
     * @param mixed $record
     * @return bool 
     */
    protected function isRecord($record) : bool
    {
        return $record instanceof Record;
    }

    /**
     * reseting the query
     */
    protected function reset() : void
    {
        $this->records = $this->allRecords;
    }

    

    // GETTING DATA

    /**
     * get all the records buy reseting the query
     */
    public function all() : array|object|string|null
    {
        $this->reset();
        return $this->records;
    }

    /**
     * getting records after the query
     */
    public function get() : array|object|string|null
    {
        return $this->records;
    }

    /**
     * getting the first record
     */
    public function first()
    {
        if($this->isEmpty()) return;
        return $this->records[array_key_first($this->records)];
    }

    /**
     * getting the last record
     */
    public function last()
    {
        if($this->isEmpty()) return;
        return $this->records[array_key_last($this->records)];
    }

    /**
     * getting all availibale columns
     * @return array
     */
    public function cols() : array
    {
        $cols = [];
        foreach($this->allRecords as $record)
        {
            if($this->isRecord($record))
            {
                $cols = array_merge($cols,$record->keys());
                $cols = array_unique($cols, SORT_REGULAR);
            }  
        }
        return $cols;
    }

    /**
     * getting the length after performing a query
     */
    public function count()
    {
        return count($this->records);
    }

    /**
     * checks if the records are empty
     * @return bool
     */
    public function isEmpty() : bool
    {
        return !$this->records;
    }







    // QUERY MEHTODS


    /**
     * select specific columns from the records that are Record instance
     * @param array $array
     * @return static
     */
    public function select(array $array) : static
    {
        $collection = [];
        foreach($this->records as $record)
        {
            if($this->isRecord($record))
            {
                $collection[] = $record->only($array);
            }
        }
        $this->records = $collection;
        return $this;
    }

    /**
     * delete records from the allRecords
     * @return static
     */
    public function destroy() : static
    {       
        $allRecords = array_udiff_assoc($this->allRecords, $this->records, function($a,$b)
        {
            if($this->isRecord($a) && $this->isRecord($b)){
                if ($a->{array_key_first($a->toArray())} === $b->{array_key_first($b->toArray())} )return 0;
                return ($a->{array_key_first($a->toArray())} > $b->{array_key_first($b->toArray())}) ? 1 : -1 ;
            }
            if($a === $b) return 0;
            return ($a > $b) ? 1 : -1 ;
        });
        $this->allRecords = array_merge($allRecords, []);
        $this->reset();
        return $this;
    }


    /**
     * pushing element to allRecords
     * @param mixed $array
     * @return static
     */
    public function push($array) : static
    {
        array_push($this->allRecords, $this->record($array));
        $this->reset();
        return $this;
    }

    /**
     * removing the last element from allRecords
     * @return static
     */
    public function pop() : static
    {
        array_pop($this->allRecords);
        $this->reset();
        return $this;
    }

    
    /**
     * filtering the records base on the $callback
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callBack) : static
    {
        $this->records = array_filter($this->records, $callBack);
        return $this;
    }

    /**
     * mapping the records base on the $callback
     * @param callable $callback
     * @return static
     */
    public function map($callBack, ...$array)
    {
        $this->records = array_map($callBack, $this->records, ...$array);
        return $this;
    }

    /**
     * skipping a number of elements
     * @param int $number
     * @return static
     */
    public function skip(int $number) : static
    {
        for($i=0; $i < $number; $i++)
        {
           array_shift($this->records);
        }
        return $this;   
    }


    /**
     * choosing matching recored based on comparator
     * @param string $key
     * @param mixed $value
     * @param string $compare
     * @return static
     */
    protected function compare(string $key, $value, string $compare = '==') : static
    {
        switch ($compare) {
            case '==':
              $this->filter(function($record) use($key, $value){
                if($this->isRecord($record)) return $record->$key == $value;
              });
              return $this;
            case '<':
                $this->filter(function($record) use($key, $value){
                   if($this->isRecord($record)) return $record->$key < $value;
                  });
                  return $this;
            case '>':
                $this->filter(function($record) use($key, $value){
                   if($this->isRecord($record)) return $record->$key > $value;
                  });
                  return $this;
            case '>=':
                $this->filter(function($record) use($key, $value){
                   if($this->isRecord($record)) return $record->$key >= $value;
                  });
                  return $this;
            case '<=':
                $this->filter(function($record) use($key, $value){
                   if($this->isRecord($record)) return $record->$key <= $value;
                  });
                  return $this;
            case '!=':
                $this->filter(function($record) use($key, $value){
                   if($this->isRecord($record)) return $record->$key != $value;
                  });
                  return $this;
            case 'like':
                $this->filter(function($record) use($key, $value){
                    if($this->isRecord($record)) return !(strpos($record->$key, $value) === false);
                });
                return $this;
            default :
                return $this;
        }
    }

    /**
     * reordering the arguments to do the compare() method
     * @param array $arg
     * @return static
     * 
     */
    public function where(...$arg) : static
    {
        if(isset($arg[2]))
        {
            return $this->compare($arg[0], $arg[2], $arg[1]);
        }
        return $this->compare($arg[0], $arg[1]);
    }

    /**
     * getting the result of the first query and second query
     * Note: recommanded to use after where() method  
     * @param array $arg
     * @return static
     * 
     */
    public function orWhere(...$arg) : static
    {
        $query1 = $this->records;
        $this->records = $this->allRecords;
        $query2 = $this->where(...$arg)->get();
        $this->records =array_unique(array_merge($query1, $query2), SORT_REGULAR);
        return $this;
    }

    /**
     * converting the records to object
     * @return object
     */
    public function toObject(): object
    {
        $records = (object) $this->records;
        return $records;
    }

    /**
     * converting the records to JSON
     * @return string
     */
    public function toJson() : string
    {
        foreach($this->records as $key => $record)
        {
            if($this->isRecord($record)){
                $this->records[$key] = $record->toArray();
            }
        }
        $records = json_encode($this->records);
        return $records;
    }

    /**
     * sorting records by  a columns
     * @param string $col
     * @param string $flag
     * @return static
     */
    public function sortBy($col, $flag='asc') : static
    {
        $sortArray=[];
        $notsorted= [];
        foreach($this->records as $key => $record)
        {
            if($this->isRecord($record)){
                $sortArray[$key] = $record->$col;
            }else{
                $notsorted[]= $record;
            } 
            
        }
        if($flag == 'asc') asort($sortArray, SORT_REGULAR);
        if($flag == 'desc') arsort($sortArray, SORT_REGULAR);
        $sortArray = array_merge($sortArray, $notsorted);

        $records=[];
        foreach($sortArray as $key => $item)
        {
            $records[$key] = array_filter($this->records, function($record) use($col, $item){
                return $record->$col == $item;
            });
        }
        $this->records = $records;
        return $this; 
    }

    /**
     * sorting records keys
     * @param string $col
     * @param string $flag
     * @return static
     */
    public function sortKeys($flag = 'asc') : static
    {
        if($flag == 'asc') ksort($this->records, SORT_REGULAR);
        if($flag == 'desc') krsort($this->records, SORT_REGULAR);
        return $this;
    }
    


}