<?php

declare(strict_types=1);

namespace App\Prompts;

use Laravel\Prompts\FormBuilder;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

final class EventFormBuilder extends FormBuilder
{
    private const string DATE_ERROR = 'The value must be a date in the format YYYY, YYYY-MM, or YYYY-MM-DD.';

    private const string DATE_PATTERN = '/^\d{4}(?:-(?:1[0-2]|0\d)(?:-(?:3[01]|[12]\d|0\d))?)?$/';

    private const string TAG_ERROR = 'Tags may contain only alphanumeric characters, dashes (-), and underscores (_).';

    private const string TAG_PATTERN = '/^[a-zA-Z0-9][\w-]{2,39}$/';

    private const string TIME_ERROR =
        'The value must be a 24-hour time with timezone offset, e.g. 09:00Z, 16:00-05, 23:59:59+06:30.';

    private const string TIME_PATTERN =
        '/^(?:2[0-3]|[01]\d)(?::[0-5]\d){1,2}(?:Z|(?:-1[0-2]|-0\d|\+1[0-4]|\+0\d)(?::[0-5]\d)?)$/';

    public function __construct()
    {
        $this->addSummary();
        $this->addDate();
        $this->addTime();
        $this->addEndDate();
        $this->addEndTime();
        $this->addLocation();
        $this->addDetails();
        $this->addNotes();
        $this->addTags();
        $this->addSources();
    }

    public function submit(): array
    {
        return ['type' => 'event'] + array_filter(
            array_filter(
                parent::submit(),
                fn ($key) => ! is_int($key) && ! str_ends_with($key, '_confirm'),
                ARRAY_FILTER_USE_KEY,
            ),
        );
    }

    private function addSummary(): void
    {
        $this->text(
            label: 'What was the event?',
            placeholder: 'e.g., PHP-Kongress 2000, PHP 5.0.0 released',
            required: true,
            validate: $this->validateSummary(...),
            hint: 'Provide a name or short summary of the event.',
            name: 'summary',
            transform: trim(...),
        );
    }

    private function addDate(): void
    {
        $this->text(
            label: 'When did the event occur?',
            placeholder: 'e.g., 1995-06-08, 2003',
            required: true,
            validate: $this->validateDate(...),
            name: 'date',
        );
    }

    private function addTime(): void
    {
        $this->text(
            label: 'If the event occurred at a specific time, when was it?',
            placeholder: 'e.g., 09:02:42Z, 12:00-05:00',
            validate: $this->validateTime(...),
            hint: "Leave blank if there's no specific time.",
            name: 'time',
            transform: trim(...),
        );
    }

    private function addEndDate(): void
    {
        $this->text(
            label: 'When did the event end?',
            placeholder: 'e.g., 1995-06-08, 2003',
            validate: $this->validateDate(...),
            hint: "Leave blank if no end date or it's the same as the start.",
            name: 'endDate',
            transform: trim(...),
        );
    }

    private function addEndTime(): void
    {
        $this->text(
            label: 'If the event ended at a specific time, when was it?',
            placeholder: 'e.g., 09:02:42Z, 12:00-05:00',
            validate: $this->validateTime(...),
            hint: "Leave blank if there's no specific end time.",
            name: 'endTime',
            transform: trim(...),
        );
    }

    private function addLocation(): void
    {
        $this->confirm(
            label: 'Did the event take place at a specific location?',
            default: false,
            name: 'location_confirm',
        );

        $this->addIf(
            condition: fn (array $responses): bool => $responses['location_confirm'] === true,
            step: fn (): array => (new LocationFormBuilder)->submit(),
            name: 'location',
        );
    }

    private function addDetails(): void
    {
        $this->textarea(
            label: 'Provide more details about the event.',
            placeholder: "Stick only to facts that can be verified through your\nsources.",
            hint: 'You may use Markdown for formatting.',
            name: 'details',
            transform: trim(...),
        );
    }

    private function addNotes(): void
    {
        $this->textarea(
            label: 'Provide any additional notes about the event.',
            placeholder: <<<'EOD'
                You may elaborate on the event beyond verifiable facts,
                e.g., explaining why it's relevant to PHP's history.
                EOD,
            hint: 'You may use Markdown for formatting.',
            name: 'notes',
            transform: trim(...),
        );
    }

    private function addTags(): void
    {
        $this->confirm(
            label: 'Do you want to add tags to the event?',
            default: false,
            hint: "Tags help organize and make sense of the data.\n  Use as many tags as you like.",
            name: 'tags_confirm',
        );

        $this->addIf(
            condition: fn (array $responses): bool => $responses['tags_confirm'] === true,
            step: $this->promptForTags(...),
            name: 'tags',
        );
    }

    private function addSources(): void
    {
        $this->note(<<<'EOD'
            You must provide at least one source that can provide historical
            verification of the event.

            You may need to dig around Archive.org or deep within the hist-
            ories of mailing lists to find appropriate sources. If a source,
            such as a book or magazine/journal article, is not available on-
            line, you may cite it, as long as you provide enough information
            that would allow someone to reasonably find it in a library or
            archives.
            EOD);

        $this->add(
            step: $this->promptForSources(...),
            name: 'sources',
        );
    }

    private function validateSummary(string $value): ?string
    {
        return match (true) {
            strlen($value) < 5 => 'Event summary must be at least 5 characters.',
            default => null,
        };
    }

    private function validateDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        return preg_match(self::DATE_PATTERN, $value) === 1 ? null : self::DATE_ERROR;
    }

    private function validateTime(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        return preg_match(self::TIME_PATTERN, $value) === 1 ? null : self::TIME_ERROR;
    }

    private function validateTag(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        return preg_match(self::TAG_PATTERN, $value) ? null : self::TAG_ERROR;
    }

    /**
     * @return list<string>
     */
    private function promptForTags(): array
    {
        $tags = [];

        do {
            $tag = text(
                label: 'Tag',
                validate: $this->validateTag(...),
                hint: 'Enter a tag; leave blank to finish adding tags.',
                transform: trim(...),
            );

            if ($tag !== '') {
                $tags[] = $tag;
            }
        } while ($tag !== '');

        return $tags;
    }

    private function promptForSources(): array
    {
        $sources = [];

        do {
            $sources[] = (new SourceFormBuilder)->submit();
        } while (confirm(label: 'Do you want to add another source?', default: false));

        return array_values(array_filter($sources));
    }
}
