<?php

use App\Prompts\LocationFormBuilder;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

describe('LocationFormBuilder', function () {

    it('accepts all empty values', function () {
        Prompt::fake([
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
        ]);

        $responses = (new LocationFormBuilder)->submit();

        expect($responses)->toBe([]);
    });

    it('accepts valid values', function () {
        $location = [
            'name' => fake()->company(),
            'city' => fake()->city(),
            'region' => fake()->state(),
            'country' => fake()->country(),
            'coordinates' => [
                'latitude' => (string) fake()->latitude(),
                'longitude' => (string) fake()->longitude(),
            ],
        ];

        Prompt::fake([
            ...str_split($location['name']), Key::ENTER,
            ...str_split($location['city']), Key::ENTER,
            ...str_split($location['region']), Key::ENTER,
            ...str_split($location['country']), Key::ENTER,
            Key::DOWN, Key::ENTER, // "Yes" to the coordinates question.
            ...str_split($location['coordinates']['latitude']), Key::ENTER,
            ...str_split($location['coordinates']['longitude']), Key::ENTER,
        ]);

        $responses = (new LocationFormBuilder)->submit();

        expect($responses)->toBe($location);
    });

    it('accepts only some values', function () {
        $venue = fake()->company();
        $city = fake()->city();
        $country = fake()->country();

        Prompt::fake([
            ...str_split($venue), Key::ENTER,
            ...str_split($city), Key::ENTER,
            Key::ENTER, // Skip region
            ...str_split($country), Key::ENTER,
            Key::ENTER, // "No" to the coordinates question.
        ]);

        $responses = (new LocationFormBuilder)->submit();

        expect($responses)->toBe([
            'name' => $venue,
            'city' => $city,
            'country' => $country,
        ]);
    });

    it('trims spaces from around values', function () {
        $venue = fake()->company();
        $city = fake()->city();
        $region = fake()->state();
        $country = fake()->country();

        Prompt::fake([
            ...str_split("         $venue  "), Key::ENTER,
            ...str_split("   $city         "), Key::ENTER,
            ...str_split("  $region        "), Key::ENTER,
            ...str_split("     $country    "), Key::ENTER,
            Key::ENTER,
        ]);

        $responses = (new LocationFormBuilder)->submit();

        expect($responses)->toBe([
            'name' => $venue,
            'city' => $city,
            'region' => $region,
            'country' => $country,
        ]);
    });

});
