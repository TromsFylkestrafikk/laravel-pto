<?php

namespace TromsFylkestrafikk\Pto\Services;

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
     * @param string $csvFileName Filename of CSV to import.
     */
    public function __construct ($csvFileName)
    {
        $this->csv = new Reader($csvFileName, 'r');
    }
}
