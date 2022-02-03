<?php

namespace TromsFylkestrafikk\Pto\Console;

use TromsFylkestrafikk\Pto\Models\Vehicle;
use TromsFylkestrafikk\Pto\Models\VehicleBus;
use TromsFylkestrafikk\Pto\Models\VehicleWatercraft;
use Illuminate\Console\Command;
use League\Csv\Reader;
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
     * List of messages collected during import.
     *
     * @var string[]
     */
    protected $messages = [];

    /**
     * Progressbar used during parsing.
     *
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progressBar = null;

    /**
     * Default type for csv import.
     *
     * @var string
     */
    protected $type;

    /**
     * Default company ID for csv import.
     *
     * @var int
     */
    protected $companyId;

    /**
     * List of supported/saved fields per vehicle type.
     *
     * @var mixed[]
     */
    public static $schema = [
        'bus' => [
            'class'                 => [ 'required' => true ],
            'brand'                 => [ 'required' => false, 'default' => '' ],
            'model'                 => [ 'required' => false, 'default' => '' ],
            'registration_id'       => [ 'required' => false, 'default' => '' ],
            'registration_year'     => [ 'required' => false, 'default' => '' ],
            'capacity_pax'          => [ 'required' => true ],
            'capacity_pax_avail'    => [ 'required' => true ],
            'capacity_seats'        => [ 'required' => false, 'default' => null ],
            'capacity_seats_avail'  => [ 'required' => false, 'default' => null ],
            'capacity_stands'       => [ 'required' => false, 'default' => null ],
            'capacity_stands_avail' => [ 'required' => false, 'default' => null ],
        ],
        'watercraft' => [
            'imo'                 => [ 'required' => false, 'default' => null ],
            'type'                => [ 'required' => true ],
            'prefix'              => [ 'required' => false, 'default' => '' ],
            'name'                => [ 'required' => true ],
            'callsign'            => [ 'required' => false, 'default' => '' ],
            'phone'               => [ 'required' => false, 'default' => '' ],
            'line'                => [ 'required' => true ],
            'capacity_pax'        => [ 'required' => true ],
            'capacity_pax_avail'  => [ 'required' => true ],
            'capacity_cars'       => [ 'required' => false, 'default' => null ],
            'capacity_cars_avail' => [ 'required' => false, 'default' => null ],
            'url'                 => [ 'required' => false, 'default' => null ],
        ],
    ];

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
        $this->companyId = $this->option('company') ?? 0;
        $this->type = $this->option('type') ?? 'bus';
        return $this->parseCsv($this->argument('file'));
    }

    /**
     * Parse CSV file.
     *
     * @param string $csvFilename
     * @param int $companyId
     * @param string $type
     *
     * @return int
     */
    public function parseCsv($csvFilename)
    {
        /** @var Reader $csv */
        $csv = Reader::createFromPath($csvFilename, 'r');
        $csv->setHeaderOffset(0);
        $this->initProgressBar($csv);
        foreach ($csv as $index => $record) {
            $this->processCsvRecord($record, $index);
            $this->progressBar->advance();
        }
        $this->finish();
        return static::SUCCESS;
    }

    /**
     * @param array $record
     */
    protected function processCsvRecord($record, $index)
    {
        if (empty($record['id'])) {
            $this->addMessage("Missing ID for record #{$index}", 'error');
            return;
        }
        $vehicle = Vehicle::find($record['id']);
        $record_type = $record['type'] ?? $this->type;
        $model_name = $record_type === 'bus' ? 'bus' : 'watercraft';
        if (!$vehicle) {
            $vehicle = new Vehicle();
            $vehicle->id = $record['id'];
            $vehicle->type = $record_type;
            $vehicle_specific = $record_type === 'bus' ? new VehicleBus() : new VehicleWatercraft();
            $vehicle_specific->id = $record['id'];
        } else {
            $vehicle_specific = $vehicle->{$model_name};
        }
        $vehicle->company_id = $record['company_id'] ?? $this->companyId;
        if (!$vehicle->company_id) {
            $this->addMessage(sprintf("%s: Missing company ID.", $vehicle->id), 'error');
            return;
        }
        $vehicle->internal_id = $record['internal_id'] ?? null;
        $vehicle->apc_enabled = $record['apc_enabled'] ?? false;
        $this->updateFromSchema($record, $vehicle_specific, self::$schema[$model_name]);
        $vehicle->save();
    }

    /**
     * Update model attributes from record using a schema.
     *
     * @param array $record
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $schema
     */
    protected function updateFromSchema($record, $model, $schema)
    {
        foreach ($schema as $field_name => $options) {
            if (isset($record[$field_name]) && strlen($record[$field_name]) !== 0) {
                $model->{$field_name} = $record[$field_name];
            } else {
                if ($options['required']) {
                    $this->addMessage(sprintf(
                        "%d: Missing required field '%s' for vehicle.",
                        $model->id,
                        $field_name
                    ), 'error');
                    return;
                }
                $model->{$field_name} = $options['default'];
            }
        }
        $model->save();
    }

    /**
     * Add a new message to be displayed to the user.
     *
     * @param string $msg  The message to display
     * @param string $severity  Error level of message: ’info’, ’warn’ or ’error’.
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

    protected function finish()
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

    protected function initProgressBar($iterable)
    {
        ProgressBar::setFormatDefinition('custom', "[ %status:-5s% ]  |%bar%| %percent:3s%% (%current%/%max%)\n%message%\n");
        $this->progressBar = $this->output->createProgressBar($iterable->count());
        $this->progressBar->setFormat('custom');
        $this->progressBar->setMessage("Importing vehicles …");
        $this->progressBar->setMessage("OK", 'status');
        $this->progressBar->start();
    }
}
