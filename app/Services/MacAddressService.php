<?php

namespace App\Services;

use App\Models\OUI;

class MacAddressService
{
    /**
     * @param  string|null  $mac
     * @return bool|string Returns true if valid, error message if invalid
     */
    public function isValidMacFormat(?string $mac): bool|string
    {
        if ($mac === null) {
            return 'MAC address is required.';
        }
        // 12 characters
        if (preg_match('/^[0-9A-Fa-f]{12}$/', $mac)) {
            return true;
        }

        // (XX:XX:XX:XX:XX:XX)
        if (preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $mac)) {
            return true;
        }

        // (XX-XX-XX-XX-XX-XX)
        if (preg_match('/^([0-9A-Fa-f]{2}-){5}[0-9A-Fa-f]{2}$/', $mac)) {
            return true;
        }

        // (XXXX.XXXX.XXXX)
        if (preg_match('/^([0-9A-Fa-f]{4}\.){2}[0-9A-Fa-f]{4}$/', $mac)) {
            return true;
        }

        return 'Invalid MAC address format. Valid formats are: XX:XX:XX:XX:XX:XX, XX-XX-XX-XX-XX-XX, XXXX.XXXX.XXXX, or XXXXXXXXXXXX';
    }

    /**
     * @param  string  $mac
     * @param  bool  $returnNullForUnknown
     * @return string|null
     */
    public function getVendorByMac(string $mac, bool $returnNullForUnknown = false): ?string
    {
        $normalizedMac = $this->normalizeMacAddress($mac);
        $oui = OUI::where('oui', substr($normalizedMac, 0, 6))->first();

        return $oui ? $oui->vendor : ($returnNullForUnknown ? null : 'Unknown');
    }

    /**
     * @param  string  $mac
     * @return string
     */
    public function normalizeMacAddress(string $mac): string
    {
        return preg_replace('/[^A-Fa-f0-9]/', '', strtoupper($mac));
    }
} 