<?php

namespace TromsFylkestrafikk\Pto\Console\Commands;

use Illuminate\Console\Command;
use TromsFylkestrafikk\Pto\Services\CsvToModels;

class CompanyImportCsv extends Command
{
    /**
     * @var \TromsFylkestrafikk\Pto\Services\CsvToModels
     */
    protected $mapper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pto:company-import-csv
                           { file : CSV file with company records }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import companies from csv file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->mapper = new CsvToModels($this->argument('file'));
    }
}
