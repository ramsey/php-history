<?php

use App\Prompts\GeoCoordinatesFormBuilder;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

describe('GeoCoordinatesFormBuilder', function () {

    $valid = [
        ['latitude' => '35.558970', 'longitude' => '139.723536'],
        ['latitude' => '90.00000000', 'longitude' => '180.00000000'],
        ['latitude' => '-90.00000000', 'longitude' => '-180.00000000'],
        ['latitude' => '0', 'longitude' => '0'],
        ['latitude' => '1', 'longitude' => '1'],
        ['latitude' => '-1', 'longitude' => '-1'],
        ['latitude' => '1.1', 'longitude' => '1.1'],
        ['latitude' => '89.99999999', 'longitude' => '179.99999999'],
        ['latitude' => '-89.99999999', 'longitude' => '-179.99999999'],
    ];

    $invalid = [
        ['invalidLat' => 'foo', 'invalidLon' => 'bar'],
        ['invalidLat' => '91', 'invalidLon' => '181'],
        ['invalidLat' => '99', 'invalidLon' => '189'],
        ['invalidLat' => '-91', 'invalidLon' => '-181'],
        ['invalidLat' => '-99', 'invalidLon' => '-189'],
        ['invalidLat' => '0.', 'invalidLon' => '0.'],
        ['invalidLat' => '+1', 'invalidLon' => '+1'],
        ['invalidLat' => '89.999999990', 'invalidLon' => '179.999999990'],
        ['invalidLat' => '-89.999999990', 'invalidLon' => '-179.999999990'],
    ];

    it('accepts valid latitude and longitude', function (string $latitude, string $longitude) {
        Prompt::fake([
            ...str_split($latitude), Key::ENTER,
            ...str_split($longitude), Key::ENTER,
        ]);

        $responses = (new GeoCoordinatesFormBuilder)->submit();

        expect($responses)->toBe([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    })->with($valid);

    it('requires latitude and longitude values', function (string $latitude, string $longitude) {
        Prompt::fake([
            // After pressing Enter, we should remain at the latitude prompt
            // until we enter a valid value, because it is required.
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
            ...str_split($latitude), Key::ENTER,

            // After pressing Enter, we should remain at the longitude prompt
            // until we enter a valid value, because it is required.
            Key::ENTER,
            Key::ENTER,
            ...str_split($longitude), Key::ENTER,
        ]);

        $responses = (new GeoCoordinatesFormBuilder)->submit();

        expect($responses)->toBe([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    })->with([$valid[0]]);

    it('requires valid latitude and longitude values', function (string $invalidLat, string $invalidLon) use ($valid) {
        Prompt::fake([
            // Enter the invalid latitude.
            ...str_split($invalidLat), Key::ENTER,
            // Backspace to clear the invalid latitude.
            ...array_fill(0, strlen($invalidLat), Key::BACKSPACE),
            // Enter the valid latitude.
            ...str_split($valid[0]['latitude']), Key::ENTER,
            // Enter the invalid longitude.
            ...str_split($invalidLon), Key::ENTER,
            // Backspace to clear the invalid longitude.
            ...array_fill(0, strlen($invalidLon), Key::BACKSPACE),
            // Enter the valid longitude.
            ...str_split($valid[0]['longitude']), Key::ENTER,
        ]);

        $responses = (new GeoCoordinatesFormBuilder)->submit();

        expect($responses)->toBe([
            'latitude' => $valid[0]['latitude'],
            'longitude' => $valid[0]['longitude'],
        ]);
    })->with($invalid);

});
