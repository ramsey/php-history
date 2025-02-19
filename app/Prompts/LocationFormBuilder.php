<?php

declare(strict_types=1);

namespace App\Prompts;

use Laravel\Prompts\FormBuilder;

final class LocationFormBuilder extends FormBuilder
{
    public function __construct()
    {
        $this->addName();
        $this->addCity();
        $this->addRegion();
        $this->addCountry();
        $this->addGeoCoordinates();
    }

    public function submit(): array
    {
        return array_filter(
            array_filter(
                parent::submit(),
                fn ($key) => ! is_int($key) && ! str_ends_with($key, '_confirm'),
                ARRAY_FILTER_USE_KEY,
            ),
        );
    }

    private function addName(): void
    {
        $this->text(
            label: 'What was the name of the venue?',
            placeholder: 'e.g., Crowne Plaza Cologne City Centre, Online (Zoom)',
            hint: 'Leave empty if there was no specific venue.',
            name: 'name',
            transform: trim(...),
        );
    }

    private function addCity(): void
    {
        $this->text(
            label: 'In what city did it take place?',
            placeholder: 'e.g., Cologne, Chicago, Montreal',
            hint: 'Leave empty if there was no specific city.',
            name: 'city',
            transform: trim(...),
        );
    }

    private function addRegion(): void
    {
        $this->text(
            label: 'In what state/province/region did it take place?',
            placeholder: 'e.g., Illinois, Quebec',
            hint: 'Leave empty if there was no specific region.',
            name: 'region',
            transform: trim(...),
        );
    }

    private function addCountry(): void
    {
        $this->text(
            label: 'In what country did it take place?',
            placeholder: 'e.g., United States, Canada, Germany',
            hint: 'Leave empty if there was no specific country.',
            name: 'country',
            transform: trim(...),
        );
    }

    private function addGeoCoordinates(): void
    {
        $this->confirm(
            label: 'Do you know the latitude/longitude of the location?',
            default: false,
            name: 'coordinates_confirm',
        );

        $this->addIf(
            condition: fn (array $responses): bool => $responses['coordinates_confirm'] === true,
            step: fn (): array => (new GeoCoordinatesFormBuilder)->submit(),
            name: 'coordinates',
        );
    }
}
