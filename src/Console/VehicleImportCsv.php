<?php

namespace TromsFylkestrafikk\Pto\Console;

use TromsFylkestrafikk\Pto\Models\Vehicle;
use TromsFylkestrafikk\Pto\Models\VehicleBus;
use TromsFylkestrafikk\Pto\Models\VehicleWatercraft;
use TromsFylkestrafikk\Pto\Services\CsvToModels;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class VehicleImportCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pto:vehicle-import-csv
                           { file : CSV file with vehicle records }
                           { --company= : Default company for vehicles if missing in csv }
                           { --type= : Default vehicle type if missing in csv }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import vehicle data from CSV file';

    /**
     * Map of allowed vehicle types and their specific model.
     */
    protected static $typeClassMap = [
        'bus' => VehicleBus::class,
        'hsc' => VehicleWatercraft::class,
        'ferry' => VehicleWatercraft::class,
    ];

    /**
     * List of messages collected during import.
     *
     * @var mixed[]
     */
    protected $messages = [];

    /**
     * Progressbar used during parsing.
     *
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progressBar = null;

    /**
     * @var string
     */
    protected $defaultType = 'bus';

    /**
     * Number of records found in CSV.
     *
     * @var int
     */
    protected $recordCount = 0;

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
        $this->defaultType = $this->option('type') ?: 'bus';
        return $this->parseCsv($this->argument('file'));
    }

    /**
     * Parse CSV file.
     *
     * @param string $csvFilename
     *
     * @return int
     */
    public function parseCsv($csvFilename)
    {
        $classesFound = [];
        $vehicleMapper = new CsvToModels($csvFilename, Vehicle::class);
        $this->recordCount = $vehicleMapper->getReader()->count();
        $this->info("Step one: Generic vehicle model import:");
        $this->initProgressBar($this->recordCount, "Importing {$this->recordCount} vehicles");
        $vehicleMapper
            ->filter(function ($record) {
                $this->progressBar->advance();
                return !isset($record['type']) || !empty(self::$typeClassMap[$record['type']]);
            })
            ->withDefaults($this->getVehicleDefaults())
            ->execute(function ($model) use (&$classesFound) {
                $classesFound[self::$typeClassMap[$model->type]] = true;
            });
        $this->summary();

        // Do it all over again for all for the detected specific vehicle types
        $this->info("Step two: Specific vehicle model import:");
        $this->initProgressBar($this->recordCount, "Processing vehicle specific content");
        foreach (array_keys($classesFound) as $typeClass) {
            $typeMapper = new CsvToModels($csvFilename, $typeClass);
            $typeMapper
                ->filter(function ($record) use ($typeClass) {
                    $recordType = $record['type'] ?? $this->defaultType;
                    return $typeClass === self::$typeClassMap[$recordType];
                })
                ->withDefaults($this->getVehicleTypeDefaults())
                ->execute(function () {
                    $this->progressBar->advance();
                });
        }
        $this->summary();
        return static::SUCCESS;
    }

    /**
     * Default record values for Vehicles.
     *
     * @return string[]
     */
    protected function getVehicleDefaults()
    {
        return [
            'type' => $this->defaultType,
            'company_id' => $this->option('company') ?: 0,
        ];
    }

    /**
     * Default record values for vehicle specific models.
     *
     * @return string[]
     */
    protected function getVehicleTypeDefaults()
    {
        return ['type' => $this->defaultType];
    }

    /**
     * Add a message on the progress bar.
     *
     * These will pool up and be summarized at the end of processing.
     *
     * @param string $msg The message to display
     * @param string $severity Error level of message: ’info’, ’warn’ or ’error’.
     */
    protected function addMessage($msg, $severity = 'info')
    {
        $this->messages[] = [
            'severity' => $severity,
            'message' => $msg,
        ];
        $this->progressBar->setMessage($msg);
        if ($severity !== 'info') {
            $this->progressBar->setMessage($severity, 'status');
        }
    }

    /**
     * Custom formatted progress bar,
     *
     * @param int $count
     */
    protected function initProgressBar($count, $message = "Importing …")
    {
        ProgressBar::setFormatDefinition('custom', "[ %status:-5s% ]  |%bar%| %percent:3s%% (%current%/%max%)\n%message%\n");
        $this->progressBar = $this->output->createProgressBar($count);
        $this->progressBar->setFormat('custom');
        $this->progressBar->setMessage($message);
        $this->progressBar->setMessage("OK", 'status');
        $this->progressBar->start();
    }

    /**
     * Post parse summary and cleanup.
     */
    protected function summary()
    {
        $this->progressBar->setMessage($this->messages ? "Complete with noise. Se log below." : "Success.");
        $this->progressBar->finish();
        foreach ($this->messages as $msg) {
            $this->{$msg['severity']}($msg['message']);
            if ($msg['severity'] !== 'info') {
                $this->progressBar->setMessage(strtoupper($msg['severity']), 'status');
            }
            $this->progressBar->setMessage($msg['message']);
        }
    }
}
