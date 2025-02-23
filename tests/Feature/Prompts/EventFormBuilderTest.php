<?php

use App\Prompts\EventFormBuilder;
use App\Services\Markdown\Normalizer;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

covers(EventFormBuilder::class);

describe('EventFormBuilder', function () {

    /**
     * Pass-through normalizer
     */
    $normalizer = new class implements Normalizer
    {
        public function convert(string $input): string
        {
            return $input;
        }
    };

    $fakeEvent = function (): array {
        $startDate = fake()->dateTimeThisCentury();

        $days = random_int(1, 7);
        $hours = random_int(1, 12);

        $endDate = clone $startDate;
        $endDate->add(new DateInterval("P{$days}DT{$hours}H"));

        return [
            'type' => 'event',
            'summary' => fake()->sentence(),
            'date' => $startDate->format('Y-m-d'),
            'time' => $startDate->format('H:ip'),
            'endDate' => $endDate->format('Y-m-d'),
            'endTime' => $endDate->format('H:ip'),
            'location' => [
                'name' => fake()->company(),
                'city' => fake()->city(),
                'region' => fake()->state(),
                'country' => fake()->country(),
                'coordinates' => [
                    'latitude' => (string) fake()->latitude(),
                    'longitude' => (string) fake()->longitude(),
                ],
            ],
            'details' => fake()->paragraph(),
            'notes' => fake()->paragraph(),
            'tags' => [fake()->word()],
            'sources' => [
                [
                    'title' => fake()->sentence(),
                    'authors' => [
                        fake()->name(),
                    ],
                    'containers' => [
                        [
                            'title' => fake()->sentence(),
                            'date' => fake()->dateTimeThisCentury()->format('j M Y'),
                            'location' => fake()->url(),
                            'contributor' => 'edited by '.fake()->name(),
                            'version' => '2nd edition',
                            'number' => 'vol. 2, no. 3',
                            'publisher' => fake()->company(),
                        ],
                    ],
                ],
            ],
        ];
    };

    it('requires summary and date values', function () use ($normalizer) {
        $eventSummary = fake()->sentence();
        $eventDate = fake()->dateTimeThisCentury()->format('Y');
        $sourceTitle = fake()->sentence();

        Prompt::fake([
            Key::ENTER, // Attempt to skip summary.
            Key::ENTER, // Attempt to skip summary, again.
            ...str_split($eventSummary), Key::ENTER, // Event summary.
            Key::ENTER, // Attempt to skip date.
            Key::ENTER, // Attempt to skip date, again.
            ...str_split($eventDate), Key::ENTER, // Event date.
            Key::ENTER, // Skip start time.
            Key::ENTER, // Skip end date.
            Key::ENTER, // Skip end time.
            Key::ENTER, // Skip location.
            Key::CTRL_D, // Skip details.
            Key::CTRL_D, // Skip notes.
            Key::ENTER, // Skip tags.
            Key::ENTER, // Attempt to skip source title.
            Key::ENTER, // Attempt to skip source title, again.
            ...str_split($sourceTitle), Key::ENTER, // Source title.
            Key::ENTER, // Skip authors.
            Key::ENTER, // Skip container title.
            Key::ENTER, // Skip container date.
            Key::ENTER, // Skip container location.
            Key::ENTER, // Skip container contributor.
            Key::ENTER, // Skip container version.
            Key::ENTER, // Skip container number.
            Key::ENTER, // Skip container publisher.
            Key::ENTER, // Proceed without adding another container.
            Key::ENTER, // Proceed without adding another source.
        ]);

        $responses = new EventFormBuilder($normalizer)->submit();

        expect($responses)->toBe([
            'type' => 'event',
            'summary' => $eventSummary,
            'date' => $eventDate,
            'sources' => [
                [
                    'title' => $sourceTitle,
                ],
            ],
        ]);
    });

    it('accepts valid values', function () use ($fakeEvent, $normalizer) {
        $event = $fakeEvent();

        Prompt::fake([
            ...str_split($event['summary']), Key::ENTER,
            ...str_split($event['date']), Key::ENTER,
            ...str_split($event['time']), Key::ENTER,
            ...str_split($event['endDate']), Key::ENTER,
            ...str_split($event['endTime']), Key::ENTER,
            Key::DOWN, Key::ENTER, // Say "yes" to the location question.
            ...str_split($event['location']['name']), Key::ENTER,
            ...str_split($event['location']['city']), Key::ENTER,
            ...str_split($event['location']['region']), Key::ENTER,
            ...str_split($event['location']['country']), Key::ENTER,
            Key::DOWN, Key::ENTER, // Say "yes" to the coordinates question.
            ...str_split($event['location']['coordinates']['latitude']), Key::ENTER,
            ...str_split($event['location']['coordinates']['longitude']), Key::ENTER,
            ...str_split($event['details']), Key::CTRL_D,
            ...str_split($event['notes']), Key::CTRL_D,
            Key::DOWN, Key::ENTER, // Say "yes" to adding tags.
            ...str_split($event['tags'][0]), Key::ENTER,
            Key::ENTER, // Done with tags.
            ...str_split($event['sources'][0]['title']), Key::ENTER,
            ...str_split($event['sources'][0]['authors'][0]), Key::ENTER,
            Key::ENTER, // Done with authors.
            ...str_split($event['sources'][0]['containers'][0]['title']), Key::ENTER,
            ...str_split($event['sources'][0]['containers'][0]['date']), Key::ENTER,
            ...str_split($event['sources'][0]['containers'][0]['location']), Key::ENTER,
            ...str_split($event['sources'][0]['containers'][0]['contributor']), Key::ENTER,
            ...str_split($event['sources'][0]['containers'][0]['version']), Key::ENTER,
            ...str_split($event['sources'][0]['containers'][0]['number']), Key::ENTER,
            ...str_split($event['sources'][0]['containers'][0]['publisher']), Key::ENTER,
            Key::ENTER, // Proceed without adding another container.
            Key::ENTER, // Proceed without adding another source.
        ]);

        $responses = new EventFormBuilder($normalizer)->submit();

        expect($responses)->toBe($event);
    });

});
