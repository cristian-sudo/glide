<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MacAddressService;
use App\Traits\ApiStructuredResponse;
use Illuminate\Http\JsonResponse;

class MacAddressController extends Controller
{
    use ApiStructuredResponse;

    private MacAddressService $macAddressService;

    public function __construct(MacAddressService $macAddressService)
    {
        $this->macAddressService = $macAddressService;
    }

    /**
     * Lookup a single MAC address.
     *
     * @param  string|null  $mac
     * @return JsonResponse
     */
    public function lookupSingle(?string $mac = null): JsonResponse
    {
        $validationResult = $this->macAddressService->isValidMacFormat($mac);
        if ($validationResult !== true) {
            return $this->errorResponse('Invalid MAC address format.', [
                'mac_address' => $validationResult,
            ], 400);
        }

        $vendor = $this->macAddressService->getVendorByMac($mac, true);

        if ($vendor === null) {
            return $this->errorResponse('MAC address not found.', [
                'mac_address' => $mac
            ], 404);
        }

        return $this->successResponse('MAC address found.', [
            'mac_address' => $mac,
            'vendor' => $vendor
        ]);
    }

    /**
     * Lookup multiple MAC addresses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookupMultiple(Request $request)
    {
        $macAddresses = $request->input('mac_addresses', []);

        if (empty($macAddresses)) {
            return $this->errorResponse('No MAC addresses provided.', [
                'mac_addresses' => 'The list of MAC addresses cannot be empty.',
            ], 400);
        }

        $normalizedToOriginal = [];
        foreach ($macAddresses as $mac) {
            $normalized = preg_replace('/[^A-Fa-f0-9]/', '', strtoupper($mac));
            $normalizedToOriginal[$normalized] = $mac;
        }

        $uniqueMacAddresses = array_values($normalizedToOriginal);

        $results = [];

        foreach ($uniqueMacAddresses as $mac) {
            $validationResult = $this->macAddressService->isValidMacFormat($mac);
            if ($validationResult !== true) {
                return $this->errorResponse('Invalid MAC address format.', [
                    'mac_addresses' => 'One or more MAC addresses have an invalid format: ' . $validationResult,
                ], 400);
            }

            $vendor = $this->macAddressService->getVendorByMac($mac);
            $results[] = [
                'mac_address' => $mac,
                'vendor' => $vendor
            ];
        }

        return $this->successResponse('MAC addresses lookup completed.', $results);
    }
}
