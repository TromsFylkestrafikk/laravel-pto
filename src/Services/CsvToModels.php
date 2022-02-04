<?php

namespace TromsFylkestrafikk\Pto\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use League\Csv\Reader;

/**
 * Create or update models from csv file.
*/
class CsvToModels
{
    /**
     * @var \League\Csv\Reader
     */
    protected $csv;

    /**
     * @var mixed[]
     */
    protected $schema;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string Primary key name of model.
     */
    protected $primaryKey;

    /**
     * True if CSV has a header matching the primary key in model.
     *
     * @var bool
     */
    protected $hasPrimaryKey;

    /**
     * @param string $csvFileName Filename of CSV to import.
     * @param array $schema
     * @param string $modelClass Model that should be updated or created.
     */
    public function __construct(string $csvFileName, array $schema, $modelClass)
    {
        $this->csv = Reader::createFromPath($csvFileName, 'r');
        $this->csv->setHeaderOffset(0);
        $this->schema = $schema;
        $this->modelClass = $modelClass;
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new Exception("Class is not subclass of Model: " . $modelClass);
        }
        /** @var \Illuminate\Database\Eloquent\Model $modelProber */
        $modelProber = new $modelClass();
        $this->primaryKey = $modelProber->getKeyName();
        $header = $this->csv->getHeader();
        $this->hasPrimaryKey = in_array($this->primaryKey, $header);
    }

    /**
     * Get current CSV reader
     *
     * @return \League\Csv\Reader
     */
    public function getReader()
    {
        return $this->csv;
    }

    /**
     * Start creating/syncing models from csv.
     */
    public function execute()
    {
        foreach ($this->csv as $record) {
            $model = $this->syncModel($record);
            $model->save();
        }
    }

    /**
     * Update model attributes from record.
     *
     * @param array $record
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function syncModel($record, $model = null)
    {
        $model = $this->getModel($record, $model);
        $model->fill($record);
        return $model;
    }

    /**
     * Get existing or create new model.
     *
     * @param array $record Used to find matching model for record.
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return null|\Illuminate\Database\Eloquent\Model
     */
    public function getModel($record, $model = null)
    {
        if ($model) {
            return $model;
        }
        if (!$this->hasPrimaryKey) {
            return new $this->modelClass();
        }
        return ($this->modelClass)::firstOrNew([$this->primaryKey => $record[$this->primaryKey]]);
    }
}
