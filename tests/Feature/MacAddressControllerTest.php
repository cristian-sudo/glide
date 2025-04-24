<?php

it('can look up a single MAC address', function () {
    $data = $this->insertOuisAddress('00:1A:2B:3C:4D:5E', 'Test Vendor');

    $response = $this->get(route('mac-address.lookup', ['mac' => $data['mac_address']]));

    $response->assertApiResponseStructure();

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'MAC address found.',
            'data' => [
                'mac_address' => $data['mac_address'],
                'vendor' => $data['vendor'],
            ],
            'errors' => null,
            'statusCode' => 200,
        ]);
});

it('returns 404 for a non-existent MAC address', function () {
    $nonExistentMacAddress = 'FF:FF:FF:FF:FF:FF';

    $response = $this->get(route('mac-address.lookup', ['mac' => $nonExistentMacAddress]));

    $response->assertApiResponseStructure();

    $response->assertStatus(404)
        ->assertJson([
            'status' => 'error',
            'message' => 'MAC address not found.',
            'data' => null,
            'errors' => [
                'mac_address' => $nonExistentMacAddress,
            ],
            'statusCode' => 404,
        ]);
});

it('returns error when no MAC address is provided', function () {
    $response = $this->get(route('mac-address.lookup'));

    $response->assertApiResponseStructure();

    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'Invalid MAC address format.',
            'data' => null,
            'errors' => [
                'mac_address' => 'MAC address is required.',
            ],
            'statusCode' => 400,
        ]);
});

it('can look up multiple MAC addresses', function () {
    $mac1 = $this->insertOuisAddress('00:1A:2B:3C:4D:5E', 'Vendor A');
    $mac2 = $this->insertOuisAddress('11:22:33:44:55:66', 'Vendor B');
    $mac3 = $this->insertOuisAddress('AA:BB:CC:DD:EE:FF', 'Vendor C');

    $macAddresses = [
        $mac1['mac_address'],
        $mac2['mac_address'],
        $mac3['mac_address'],
    ];

    $response = $this->postJson(route('mac-addresses.lookup'), ['mac_addresses' => $macAddresses]);

    $response->assertApiResponseStructure();

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'MAC addresses lookup completed.',
            'data' => [
                ['mac_address' => $mac1['mac_address'], 'vendor' => $mac1['vendor']],
                ['mac_address' => $mac2['mac_address'], 'vendor' => $mac2['vendor']],
                ['mac_address' => $mac3['mac_address'], 'vendor' => $mac3['vendor']],
            ],
            'errors' => null,
            'statusCode' => 200,
        ]);
});

it('handles an empty list of MAC addresses', function () {
    $emptyMacAddresses = [];

    $response = $this->postJson(route('mac-addresses.lookup'), ['mac_addresses' => $emptyMacAddresses]);

    $response->assertApiResponseStructure();

    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'No MAC addresses provided.',
            'data' => null,
            'errors' => [
                'mac_addresses' => 'The list of MAC addresses cannot be empty.',
            ],
            'statusCode' => 400,
        ]);
});

it('returns an error for an invalid MAC address format in lookupSingle', function () {
    $invalidMacAddress = 'Invalid-MAC-Address';

    $response = $this->get(route('mac-address.lookup', ['mac' => $invalidMacAddress]));

    $response->assertApiResponseStructure();

    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'Invalid MAC address format.',
            'data' => null,
            'errors' => [
                'mac_address' => 'Invalid MAC address format. Valid formats are: XX:XX:XX:XX:XX:XX, XX-XX-XX-XX-XX-XX, XXXX.XXXX.XXXX, or XXXXXXXXXXXX',
            ],
            'statusCode' => 400,
        ]);
});

it('returns an error for an invalid MAC address format in lookupMultiple', function () {
    $invalidMacAddresses = ['Invalid-MAC-Address'];

    $response = $this->postJson(route('mac-addresses.lookup'), ['mac_addresses' => $invalidMacAddresses]);

    $response->assertApiResponseStructure();

    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'Invalid MAC address format.',
            'data' => null,
            'errors' => [
                'mac_addresses' => 'One or more MAC addresses have an invalid format: Invalid MAC address format. Valid formats are: XX:XX:XX:XX:XX:XX, XX-XX-XX-XX-XX-XX, XXXX.XXXX.XXXX, or XXXXXXXXXXXX',
            ],
            'statusCode' => 400,
        ]);
});

it('handles duplicate MAC addresses in the request', function () {
    $this->insertOuisAddress('AA:BB:CC:DD:EE:FF', 'Test Vendor');

    $duplicateMacAddresses = [
        'AA:BB:CC:DD:EE:FF',
        'AA:BB:CC:DD:EE:FF',
        'FF:EE:DD:11:22:33',
    ];

    $response = $this->postJson(route('mac-addresses.lookup'), ['mac_addresses' => $duplicateMacAddresses]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'MAC addresses lookup completed.',
            'data' => [
                [
                    'mac_address' => 'AA:BB:CC:DD:EE:FF',
                    'vendor' => 'Test Vendor',
                ],
                [
                    'mac_address' => 'FF:EE:DD:11:22:33',
                    'vendor' => 'Unknown',
                ],
            ],
            'statusCode' => 200,
        ]);
});

it('can look up a MAC address without separators', function () {
    $this->insertOuisAddress('20:15:82:00:00:00', 'Apple Inc.');

    $response = $this->get(route('mac-address.lookup', ['mac' => '2015821A0E60']));

    $response->assertApiResponseStructure();

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'MAC address found.',
            'data' => [
                'mac_address' => '2015821A0E60',
                'vendor' => 'Apple Inc.',
            ],
            'statusCode' => 200,
        ]);
});

it('can look up multiple MAC addresses without separators', function () {
    $this->insertOuisAddress('20:15:82:00:00:00', 'Apple Inc.');
    $this->insertOuisAddress('00:1A:2B:00:00:00', 'Test Vendor');

    $macAddresses = [
        '2015821A0E60',
        '001A2B3C4D5E',
    ];

    $response = $this->postJson(route('mac-addresses.lookup'), ['mac_addresses' => $macAddresses]);

    $response->assertApiResponseStructure();

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'MAC addresses lookup completed.',
            'data' => [
                [
                    'mac_address' => '2015821A0E60',
                    'vendor' => 'Apple Inc.',
                ],
                [
                    'mac_address' => '001A2B3C4D5E',
                    'vendor' => 'Test Vendor',
                ],
            ],
            'statusCode' => 200,
        ]);
});

it('can look up MAC addresses in mixed formats', function () {
    $this->insertOuisAddress('20:15:82:00:00:00', 'Apple Inc.');
    $this->insertOuisAddress('00:1A:2B:00:00:00', 'Test Vendor');
    $this->insertOuisAddress('AA:BB:CC:00:00:00', 'Another Vendor');

    $macAddresses = [
        '2015821A0E60',         
        '00:1A:2B:3C:4D:5E',    
        'AA-BB-CC-DD-EE-FF',      
        '2015821A0E60',           
        '00:1A:2B:3C:4D:5E',      
    ];

    $response = $this->postJson(route('mac-addresses.lookup'), ['mac_addresses' => $macAddresses]);

    $response->assertApiResponseStructure();

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'MAC addresses lookup completed.',
            'data' => [
                [
                    'mac_address' => '2015821A0E60',
                    'vendor' => 'Apple Inc.',
                ],
                [
                    'mac_address' => '00:1A:2B:3C:4D:5E',
                    'vendor' => 'Test Vendor',
                ],
                [
                    'mac_address' => 'AA-BB-CC-DD-EE-FF',
                    'vendor' => 'Another Vendor',
                ],
            ],
            'statusCode' => 200,
        ]);
});
it('deduplicates MAC addresses in different formats', function () {
    $this->insertOuisAddress('20:15:82:00:00:00', 'Apple Inc.');

    $macAddresses = [
        '20:15:82:1A:0E:60',
        '2015821A0E60',
        '20-15-82-1A-0E-60',
        '2015821A0E60'
    ];

    $response = $this->postJson(route('mac-addresses.lookup'), ['mac_addresses' => $macAddresses]);

    $response->assertApiResponseStructure();

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'MAC addresses lookup completed.',
            'data' => [
                [
                    'mac_address' => '2015821A0E60',
                    'vendor' => 'Apple Inc.',
                ],
            ],
            'errors' => null,
            'statusCode' => 200,
        ])
        ->assertJsonCount(1, 'data');
});

it('validates different MAC address formats correctly', function () {
    $this->insertOuisAddress('00:1A:2B:00:00:00', 'Test Vendor');

    $response = $this->get(route('mac-address.lookup', ['mac' => '00:1A:2B:3C:4D:5E']));
    $response->assertStatus(200);

    $response = $this->get(route('mac-address.lookup', ['mac' => '00-1A-2B-3C-4D-5E']));
    $response->assertStatus(200);

    $response = $this->get(route('mac-address.lookup', ['mac' => '001A.2B3C.4D5E']));
    $response->assertStatus(200);

    $response = $this->get(route('mac-address.lookup', ['mac' => '001A2B3C4D5E']));
    $response->assertStatus(200);
});

it('rejects invalid MAC address formats', function () {
    $response = $this->get(route('mac-address.lookup', ['mac' => '00.1A.2B.3C.4D.5E']));
    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'Invalid MAC address format.',
            'errors' => [
                'mac_address' => 'Invalid MAC address format. Valid formats are: XX:XX:XX:XX:XX:XX, XX-XX-XX-XX-XX-XX, XXXX.XXXX.XXXX, or XXXXXXXXXXXX',
            ],
        ]);

    $response = $this->get(route('mac-address.lookup', ['mac' => '00-1A-2B-3C-4D']));
    $response->assertStatus(400);

    $response = $this->get(route('mac-address.lookup', ['mac' => '00:1A:2B:3C:4D:5E:6F']));
    $response->assertStatus(400);

    $response = $this->get(route('mac-address.lookup', ['mac' => '00:1A-2B.3C:4D-5E']));
    $response->assertStatus(400);

    $response = $this->get(route('mac-address.lookup', ['mac' => '00:1A:2B:3C:4D:GG']));
    $response->assertStatus(400);
});

it('handles case-insensitive MAC addresses', function () {
    $this->insertOuisAddress('00:1A:2B:00:00:00', 'Test Vendor');

    $response = $this->get(route('mac-address.lookup', ['mac' => '00:1a:2b:3c:4d:5e']));
    $response->assertStatus(200);

    $response = $this->get(route('mac-address.lookup', ['mac' => '00:1A:2b:3C:4d:5E']));
    $response->assertStatus(200);
});

