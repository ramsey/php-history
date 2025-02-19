<?php

use App\Prompts\ContainerFormBuilder;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

describe('ContainerFormBuilder', function () {

    $location = [
        fake()->url(),
        fake()->company(),
        'pp. 123-456',
        'p. '.(fake()->randomNumber(3)),
        '10.1000/182',
    ];

    $publicationDate = [
        fake()->dateTimeThisCentury()->format('Y'),
        fake()->dateTimeThisCentury()->format('j F Y'),
        fake()->dateTimeThisCentury()->format('F j, Y'),
        fake()->dateTimeThisCentury()->format('M Y'),
        'Spring '.fake()->dateTimeThisCentury()->format('Y'),
        'Winter '.fake()->dateTimeThisCentury()->format('Y'),
    ];

    $version = [
        'version '.fake()->semver(),
        '2nd edition',
        '9th edition, e-book edition',
        'e-book edition',
    ];

    $number = [
        'season 2, episode 3',
        'volume 37, issue 8',
        'vol. 3, no. 7',
    ];

    $contributor = [
        'edited by '.fake()->name(),
        'translated by '.fake()->name(),
    ];

    $fakeContainer = fn (): array => [
        'title' => fake()->sentence(),
        'date' => $publicationDate[random_int(0, count($publicationDate) - 1)],
        'location' => $location[random_int(0, count($location) - 1)],
        'contributor' => $contributor[random_int(0, count($contributor) - 1)],
        'version' => $version[random_int(0, count($version) - 1)],
        'number' => $number[random_int(0, count($number) - 1)],
        'publisher' => fake()->company(),
    ];

    it('accepts all empty values', function () {
        Prompt::fake([
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
            Key::ENTER,
        ]);

        $responses = (new ContainerFormBuilder)->submit();

        expect($responses)->toBe([]);
    });

    it('accepts valid values', function () use ($fakeContainer) {
        $container = $fakeContainer();

        Prompt::fake([
            ...str_split($container['title']), Key::ENTER,
            ...str_split($container['date']), Key::ENTER,
            ...str_split($container['location']), Key::ENTER,
            ...str_split($container['contributor']), Key::ENTER,
            ...str_split($container['version']), Key::ENTER,
            ...str_split($container['number']), Key::ENTER,
            ...str_split($container['publisher']), Key::ENTER,
        ]);

        $responses = (new ContainerFormBuilder)->submit();

        expect($responses)->toBe($container);
    });

    it('accepts only some values', function () use ($fakeContainer) {
        $container = $fakeContainer();

        Prompt::fake([
            ...str_split($container['title']), Key::ENTER,
            Key::ENTER,
            ...str_split($container['location']), Key::ENTER,
            Key::ENTER,
            ...str_split($container['version']), Key::ENTER,
            Key::ENTER,
            Key::ENTER,
        ]);

        $responses = (new ContainerFormBuilder)->submit();

        expect($responses)->toBe([
            'title' => $container['title'],
            'location' => $container['location'],
            'version' => $container['version'],
        ]);
    });

    it('trims spaces from around values', function () use ($fakeContainer) {
        $container = $fakeContainer();

        Prompt::fake([
            ...str_split("  {$container['title']}      "), Key::ENTER,
            ...str_split("     {$container['date']}    "), Key::ENTER,
            ...str_split(" {$container['location']}    "), Key::ENTER,
            ...str_split(" {$container['contributor']} "), Key::ENTER,
            ...str_split("    {$container['version']}  "), Key::ENTER,
            ...str_split("   {$container['number']}    "), Key::ENTER,
            ...str_split("  {$container['publisher']}  "), Key::ENTER,
        ]);

        $responses = (new ContainerFormBuilder)->submit();

        expect($responses)->toBe($container);
    });

});
