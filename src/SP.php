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

    public function __construct()
    {
        $this->defaultConnection();
    }

    /**
     * Setting up database default connection.
     */
    private function defaultConnection()
    {
        $this->database = config('sproc.default');
    }

    /**
     * Specify database connection.
     */
    public function connect($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Specify stored procedure name to be called.
     */
    public function call($stored_procedure)
    {
        $this->stored_procedure = $stored_procedure;
        return $this;
    }

    /**
     * Parameters and Arguments to be passed into stored procedure.
     */
    public function params($params = [])
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
     * Query Builder.
     */
    protected function buildQuery()
    {
        $std_query = "EXECUTE $this->stored_procedure";
        return isset($this->params->query) ? "$std_query " . $this->params->query : $std_query;
    }

    /**
     * Returns Query.
     */
    public function toSql()
    {
        return $this->buildQuery();
    }

    /**
     * Execute stored procedure and retrieving dataset results.
     */
    protected function executeStmt()
    {
        $stmt = DB::connection($this->database)
            ->getPdo()
            ->prepare($this->buildQuery());

        // parameter binding
        if(isset($this->params->raw)){
            foreach ($this->params->raw as $key => $value){
                $stmt->bindParam(":$key", $this->params->raw[$key]);
            }
        }

        $stmt->execute();

        return $stmt;
    }

    /**
     * Retrieving raw dataset results
     * before converting into Laravel Collection Method.
     */
    public function fetch(){
        if(empty($this->results)){
            $stmt = $this->executeStmt();

            // populating into array
            do {
                $dataset = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if ($dataset) {
                    // hydrating each dataset, so it can leverage the eloquent collection functionality
                    $this->results[] = $this->hydrate($dataset);
                }
            } while ($stmt->nextRowset());

            unset($stmt);
        }

        // convert datasets into a collection
        $this->results = collect($this->results);

        return $this;
    }

    /**
     * Get specific dataset.
     */
    private function getDataset($index = null)
    {
        if($this->results->isEmpty() || is_null($index)){
            return $this->results;
        }

        return $this->results[$index];
    }

    /**
     * Hydrating data using in-package Model.
     */
    public function hydrate($dataset){
        return Hydrate::hydrate($dataset);
    }

    /**
     * Mimics Laravel Collection Method.
     * Limited only to the first dataset result if not using a get() method.
     */
    public function first(){
        return $this->fetch()->getDataset(0)->first();
    }

    /**
     * Specify what dataset to retrieve, default index is 0.
     */
    public function get($index = 0)
    {
        return $this->fetch()->getDataset($index);
    }

    /**
     * Returning all dataset results.
     */
    public function all()
    {
        return $this->fetch()->getDataset();
    }

    /**
     * Method to be used if no expected result to be returned.
     */
    public function execute()
    {
        $this->executeStmt();
    }

    /**
     * Execute and fetch scope identity.
     */
    public function getScopeID()
    {
        $result = $this->fetch()->getDataset(0);

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
}
