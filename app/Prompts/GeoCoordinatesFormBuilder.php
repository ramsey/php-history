<?php

declare(strict_types=1);

namespace App\Prompts;

use Laravel\Prompts\FormBuilder;

final class GeoCoordinatesFormBuilder extends FormBuilder
{
    private const string LATITUDE_ERROR = 'The value must be formatted as latitude, e.g. 35.558970';

    private const string LATITUDE_PATTERN = '/^-?[1-9]?(?(?<=9)0|[0-9])(?:\.\d{1,8})?$/';

    private const string LONGITUDE_ERROR = 'The value must be formatted as longitude, e.g. 139.723536';

    private const string LONGITUDE_PATTERN = '/^-?(?:1[0-8]|[1-9])?(?(?<=18)0|[0-9])(?:\.\d{1,8})?$/';

    public function __construct()
    {
        $this->addLatitude();
        $this->addLongitude();
    }

    private function addLatitude(): void
    {
        $this->text(
            label: 'Latitude',
            placeholder: 'e.g., 35.558970, 50.936099',
            required: true,
            validate: $this->validateLatitude(...),
            name: 'latitude',
        );
    }

    private function addLongitude(): void
    {
        $this->text(
            label: 'Longitude',
            placeholder: 'e.g., 139.723536, 6.938516',
            required: true,
            validate: $this->validateLongitude(...),
            name: 'longitude',
        );
    }

    private function validateLatitude(string $value): ?string
    {
        return preg_match(self::LATITUDE_PATTERN, $value) === 1 ? null : self::LATITUDE_ERROR;
    }

    private function validateLongitude(string $value): ?string
    {
        return preg_match(self::LONGITUDE_PATTERN, $value) === 1 ? null : self::LONGITUDE_ERROR;
    }
}
