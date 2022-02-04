<?php

namespace TromsFylkestrafikk\Pto\Console;

use Illuminate\Console\Command;
use TromsFylkestrafikk\Pto\Services\CsvToModels;
use TromsFylkestrafikk\Pto\Models\Company;

class CompanyImportCsv extends Command
{
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
        $mapper = new CsvToModels($this->argument('file'), Company::class);
        $mapper->execute();
        $this->info(sprintf("Successfully imported %d companies", $mapper->getSynced()));
    }
}
