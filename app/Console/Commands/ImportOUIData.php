<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OUI;
use Illuminate\Support\Facades\Http;

class ImportOUIData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ouidata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the latest IEEE OUI data into the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting the import of OUI data...');

        $url = 'http://standards-oui.ieee.org/oui/oui.csv'; // I would put this in an env variable as an improvement

        try {
            $response = Http::get($url);

            if ($response->failed()) {
                $this->error('Failed to download the OUI data.');
                return 1;
            }

            $csvData = $response->body();
            $lines = explode("\n", $csvData);

            // Skip the header line
            array_shift($lines);

            foreach ($lines as $line) {
                $fields = str_getcsv($line);

                if (count($fields) < 3) {
                    continue;
                }

                $oui = trim($fields[1]);
                $vendor = trim($fields[2]);

                if (!empty($oui) && !empty($vendor)) {
                    OUI::updateOrCreate(
                        ['oui' => $oui],
                        ['vendor' => $vendor]
                    );
                }
            }

            $this->info('OUI data import completed successfully.');
            return 0;

        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
