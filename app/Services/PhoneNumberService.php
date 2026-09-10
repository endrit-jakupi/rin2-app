<?php

namespace App\Services;

use MessageBird\Bird;
use MessageBird\Wire\Model\PhoneNumberLookupRequest;

class PhoneNumberService
{
    // Check whether the phone number is identified as a mobile number.
    public function isMobile(string $phoneNumber): bool
    {
        $bird = new Bird(config('services.bird.api_key'));

        $request = new PhoneNumberLookupRequest();
        $request->setPhoneNumber($phoneNumber);

        $result = $bird->lookup->phoneNumber($request);

        return $result->getLineType() === 'mobile';
    }
}