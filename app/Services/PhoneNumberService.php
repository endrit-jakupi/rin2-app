<?php

namespace App\Services;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberService
{
    // Check whether the phone number is valid and identified as mobile.
    public function isMobile(string $phoneNumber): bool
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $number = $phoneUtil->parse($phoneNumber);

            if (! $phoneUtil->isValidNumber($number)) {
                return false;
            }

            return $phoneUtil->getNumberType($number) === PhoneNumberType::MOBILE;
        } catch (NumberParseException) {
            return false;
        }
    }
}