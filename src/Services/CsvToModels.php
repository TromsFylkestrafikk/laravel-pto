<?php

namespace TromsFylkestrafikk\Pto\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use League\Csv\Reader;

/**
 * Create or update models from csv file.
 *
 * CSV must have a headers on first line, and they have to match exactly the
 * names of the model to map to.
 *
 * Import will update existing models if CSV has a column matching the primary
 * key for the model, otherwise it will create new models on every import.
 */
class CsvToModels
{
    /**
     * @var \League\Csv\Reader
     */
    protected $csv;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string Primary key name of model.
     */
    protected $keyName;

    /**
     * True if CSV has a header matching the primary key in model.
     *
     * @var bool
     */
    protected $hasPrimaryKey;

    /**
     * @var int
     */
    protected $synced;

    /**
     * If present, only update records if this yields true.
     *
     * @var callable
     */
    protected $whenHandler;

    /**
     * Keyed array with default values for record;
     *
     * @var array
     */
    protected $defaults;

    /**
     * @param string $csvFileName Filename of CSV to import.
     * @param string $modelClass Model that should be updated or created.
     */
    public function __construct(string $csvFileName, $modelClass)
    {
        $this->csv = Reader::createFromPath($csvFileName, 'r');
        $this->csv->setHeaderOffset(0);
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new Exception("Class is not subclass of Model: " . $modelClass);
        }
        $this->modelClass = $modelClass;
        /** @var \Illuminate\Database\Eloquent\Model $modelProber */
        $modelProber = new $modelClass();
        $this->keyName = $modelProber->getKeyName();
        $header = $this->csv->getHeader();
        $this->hasPrimaryKey = in_array($this->keyName, $header);
        $this->synced = 0;
        $this->defaults = [];
        $this->whenHandler = null;
    }

    public function filter(callable $whenHandler)
    {
        $this->whenHandler = $whenHandler;
        return $this;
    }

    public function withDefaults(array $defaults)
    {
        $this->defaults = $defaults;
        return $this;
    }

    /**
     * Start creating/syncing models from csv.
     *
     * @param null|callable $recordCallback
     *   Called on every successfully processed records with the updated model
     *   and record as arguments.
     */
    public function execute($recordCallback = null)
    {
        foreach ($this->csv as $record) {
            if ($this->whenHandler && !call_user_func($this->whenHandler, $record)) {
                continue;
            }
            $model = $this->syncModel($record);
            $model->save();
            if ($recordCallback) {
                $recordCallback($model, $record);
            }
            $this->synced++;
        }
        return $this;
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
        $model->fill(array_merge($this->defaults, $record));
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
        $primaryKey = $record[$this->keyName];
        if (!$primaryKey) {
            throw new Exception("Primary key is missing in record");
        }
        $model = ($this->modelClass)::find($primaryKey);
        if (!$model) {
            $model = new $this->modelClass();
            $model->{$this->keyName} = $primaryKey;
        }
        return $model;
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
     * @return int
     */
    public function getSynced()
    {
        return $this->synced;
    }
}
