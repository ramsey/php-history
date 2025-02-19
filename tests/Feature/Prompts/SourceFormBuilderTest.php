<?php

use App\Prompts\SourceFormBuilder;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

describe('SourceFormBuilder', function () {

    $fakeContainer = fn (): array => [
        'title' => fake()->sentence(),
        'date' => fake()->dateTimeThisCentury()->format('j M Y'),
        'location' => fake()->url(),
        'contributor' => 'edited by '.fake()->name(),
        'version' => '2nd edition',
        'number' => 'vol. 2, no. 3',
        'publisher' => fake()->company(),
    ];

    it('requires title value', function () {
        $title = fake()->sentence();

        Prompt::fake([
            Key::ENTER, // Attempt to skip title, 1.
            Key::ENTER, // Attempt to skip title, 2.
            ...str_split($title), Key::ENTER, // Add a title.
            Key::ENTER, // Skip authors.
            Key::ENTER, // Skip container title.
            Key::ENTER, // Skip container date.
            Key::ENTER, // Skip container location.
            Key::ENTER, // Skip container contributor.
            Key::ENTER, // Skip container version.
            Key::ENTER, // Skip container number.
            Key::ENTER, // Skip container publisher.
            Key::ENTER, // Proceed without adding another container.
        ]);

        $responses = (new SourceFormBuilder)->submit();

        expect($responses)->toBe([
            'title' => $title,
        ]);
    });

    it('accepts valid values', function () use ($fakeContainer) {
        $source = [
            'title' => fake()->sentence(),
            'authors' => [
                fake()->name(),
            ],
            'containers' => [
                $fakeContainer(),
            ],
        ];

        Prompt::fake([
            ...str_split($source['title']), Key::ENTER,
            ...str_split($source['authors'][0]), Key::ENTER,
            Key::ENTER, // No more authors.
            ...str_split($source['containers'][0]['title']), Key::ENTER,
            ...str_split($source['containers'][0]['date']), Key::ENTER,
            ...str_split($source['containers'][0]['location']), Key::ENTER,
            ...str_split($source['containers'][0]['contributor']), Key::ENTER,
            ...str_split($source['containers'][0]['version']), Key::ENTER,
            ...str_split($source['containers'][0]['number']), Key::ENTER,
            ...str_split($source['containers'][0]['publisher']), Key::ENTER,
            Key::ENTER, // No more containers.
        ]);

        $responses = (new SourceFormBuilder)->submit();

        expect($responses)->toBe($source);
    });

    it('accepts valid values with multiple authors and containers', function () use ($fakeContainer) {
        $source = [
            'title' => fake()->sentence(),
            'authors' => [
                fake()->name(),
                fake()->name(),
                fake()->name(),
            ],
            'containers' => [
                $fakeContainer(),
                $fakeContainer(),
                $fakeContainer(),
            ],
        ];

        Prompt::fake([
            ...str_split($source['title']), Key::ENTER,
            ...str_split($source['authors'][0]), Key::ENTER,
            ...str_split($source['authors'][1]), Key::ENTER,
            ...str_split($source['authors'][2]), Key::ENTER,
            Key::ENTER, // No more authors.
            ...str_split($source['containers'][0]['title']), Key::ENTER,
            ...str_split($source['containers'][0]['date']), Key::ENTER,
            ...str_split($source['containers'][0]['location']), Key::ENTER,
            ...str_split($source['containers'][0]['contributor']), Key::ENTER,
            ...str_split($source['containers'][0]['version']), Key::ENTER,
            ...str_split($source['containers'][0]['number']), Key::ENTER,
            ...str_split($source['containers'][0]['publisher']), Key::ENTER,
            Key::DOWN, Key::ENTER, // Enter another container.
            ...str_split($source['containers'][1]['title']), Key::ENTER,
            ...str_split($source['containers'][1]['date']), Key::ENTER,
            ...str_split($source['containers'][1]['location']), Key::ENTER,
            ...str_split($source['containers'][1]['contributor']), Key::ENTER,
            ...str_split($source['containers'][1]['version']), Key::ENTER,
            ...str_split($source['containers'][1]['number']), Key::ENTER,
            ...str_split($source['containers'][1]['publisher']), Key::ENTER,
            Key::DOWN, Key::ENTER, // Enter another container.
            ...str_split($source['containers'][2]['title']), Key::ENTER,
            ...str_split($source['containers'][2]['date']), Key::ENTER,
            ...str_split($source['containers'][2]['location']), Key::ENTER,
            ...str_split($source['containers'][2]['contributor']), Key::ENTER,
            ...str_split($source['containers'][2]['version']), Key::ENTER,
            ...str_split($source['containers'][2]['number']), Key::ENTER,
            ...str_split($source['containers'][2]['publisher']), Key::ENTER,
            Key::ENTER, // No more containers.
        ]);

        $responses = (new SourceFormBuilder)->submit();

        expect($responses)->toBe($source);
    });

    it('accepts only some values', function () use ($fakeContainer) {
        $container = $fakeContainer();
        $source = [
            'title' => fake()->sentence(),
            'containers' => [
                [
                    'date' => $container['date'],
                    'location' => $container['location'],
                ],
            ],
        ];

        Prompt::fake([
            ...str_split($source['title']), Key::ENTER,
            Key::ENTER, // No authors.
            Key::ENTER, // No container title.
            ...str_split($source['containers'][0]['date']), Key::ENTER,
            ...str_split($source['containers'][0]['location']), Key::ENTER,
            Key::ENTER, // No container contributor.
            Key::ENTER, // No container version.
            Key::ENTER, // No container number.
            Key::ENTER, // No container publisher.
            Key::ENTER, // No more containers.
        ]);

        $responses = (new SourceFormBuilder)->submit();

        expect($responses)->toBe($source);
    });

    it('trims spaces from around values', function () {
        $title = fake()->sentence();
        $author1 = fake()->name();
        $author2 = fake()->name();

        Prompt::fake([
            ...str_split("      $title    "), Key::ENTER, // Add a title.
            ...str_split("    $author1    "), Key::ENTER, // First author.
            ...str_split("       $author2 "), Key::ENTER, // Second author.
            Key::ENTER, // No more authors.
            Key::ENTER, // Skip container title.
            Key::ENTER, // Skip container date.
            Key::ENTER, // Skip container location.
            Key::ENTER, // Skip container contributor.
            Key::ENTER, // Skip container version.
            Key::ENTER, // Skip container number.
            Key::ENTER, // Skip container publisher.
            Key::ENTER, // Proceed without adding another container.
        ]);

        $responses = (new SourceFormBuilder)->submit();

        expect($responses)->toBe([
            'title' => $title,
            'authors' => [
                $author1,
                $author2,
            ],
        ]);
    });

});
