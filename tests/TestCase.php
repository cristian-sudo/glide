<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\OUI;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public string $fixturesFolder = 'tests/fixtures';

    /**
     * Constructor.
     *
     * Initializes the SQLite connection resolver to use custom logic
     * for handling schema operations that are not natively supported by SQLite.
     *
     * @param string|null $name The name of the test case.
     * @param array $data The data associated with the test.
     * @param string $dataName The name of the dataset.
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    function insertOuisAddress($macAddress, $vendorName)
    {
        $normalizedMac = strtoupper(substr(preg_replace('/[^A-Fa-f0-9]/', '', $macAddress), 0, 6));
        OUI::create([
            'oui' => $normalizedMac,
            'vendor' => $vendorName,
        ]);

        return [
            'mac_address' => $macAddress,
            'vendor' => $vendorName,
        ];
    }
}
