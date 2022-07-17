<?php

namespace Masterei\Sproc;

use Illuminate\Support\Facades\DB;
use Masterei\Sproc\Models\Hydrate;

class SP
{
    protected $database;

    protected $stored_procedure;

    protected $params;

    protected $results = [];

    protected $pdo_execute = false;

    public function __construct()
    {
        $this->defaultConnection();
    }

    /**
     * Setting up database default connection.
     * @void
     */
    private function defaultConnection()
    {
        $this->database = config('sproc.default');
    }

    /**
     * Specify a database connection.
     * @param $database
     * @return $this
     */
    public function connect($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Specify stored procedure name to be called.
     * @param $stored_procedure
     * @return $this
     */
    public function call($stored_procedure)
    {
        $this->stored_procedure = $stored_procedure;
        return $this;
    }

    /**
     * Parameters and Arguments to be passed into stored procedure.
     * @param array $params
     * @return $this
     */
    public function params(array $params = [])
    {
        $temp = [];
        foreach (array_keys($params) as $key){
            $temp[] = "@$key=:$key";
        }

        $this->params = (object) [
            'raw' => $params,
            'query' => implode(', ', $temp),
        ];

        return $this;
    }

    /**
     * Execute stored procedure and retrieving dataset results.
     * @return mixed
     */
    protected function executeStmt()
    {
        $query = "EXECUTE $this->stored_procedure";
        $query .= isset($this->params->query) ? " " . $this->params->query : null;

        $stmt = DB::connection($this->database)->getPdo()->prepare($query);

        // parameter binding
        if(isset($this->params->raw)){
            foreach ($this->params->raw as $key => $value){
                $stmt->bindParam(":$key", $value);
            }
        }

        $stmt->execute();

        return $stmt;
    }

    /**
     * Retrieving raw dataset results
     * before converting into Laravel Collection Method.
     * @return $this
     */
    public function fetch(){
        if(empty($this->results)){
            $stmt = $this->executeStmt();

            // populate into array
            do {
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                if ($rows) {
                    $this->results[] = $rows;
                }
            } while ($stmt->nextRowset());
        }

        return $this;
    }

    /**
     * Get specific dataset.
     * @param null $index
     * @return array
     */
    private function getDataset($index = null)
    {
        if(empty($this->results)){
            return [];
        }

        if(is_null($index)){
            return $this->results;
        }

        return $this->results[$index];
    }

    /**
     * Hydrating data using in package Model.
     * @param null $dataset
     * @return mixed
     */
    public function hydrate($dataset){
        return Hydrate::hydrate($dataset);
    }

    /**
     * Mimics Laravel Collection Method.
     * Limited only to the first dataset result if not using a get() method.
     * @return mixed
     */
    public function first(){
        return $this->hydrate($this->fetch()->getDataset(0))->first();
    }

    /**
     * Specify what dataset to be retrieve, default index is 0.
     * @param int $index
     * @return $this
     */
    public function get($index = 0)
    {
        return $this->hydrate($this->fetch()->getDataset($index));
    }

    /**
     * Returning all dataset results.
     * @return $this
     */
    public function all()
    {
        return $this->hydrate($this->fetch()->getDataset());
    }

    /**
     * Method to be used if no expected result to be returned.
     * @return $this
     */
    public function execute()
    {
        $this->fetch();
        $this->pdo_execute = true;

        return $this;
    }

    /**
     * Returning inserted id after chaining with execute() method.
     * @return int|mixed
     */
    public function getLastInsertedId()
    {
        $result = $this->hydrate($this->getDataset());

        if(!$result->isEmpty()){
            $result = $result->first();

            // ID
            if(isset($result->ID)){
                return $result->ID;
            }

            // id
            if(isset($result->id)){
                return $result->id;
            }

            return array_values($result->toArray())[0];
        }

        return 0;
    }

    /**
     * PDO status for successful execution.
     * @return string
     */
    public function __toString()
    {
        return (string) $this->pdo_execute;
    }
}
