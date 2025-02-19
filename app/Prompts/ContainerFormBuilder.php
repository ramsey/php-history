<?php

declare(strict_types=1);

namespace App\Prompts;

use Laravel\Prompts\FormBuilder;

final class ContainerFormBuilder extends FormBuilder
{
    public function __construct()
    {
        $this->addTitle();
        $this->addPublicationDate();
        $this->addLocation();
        $this->addContributor();
        $this->addVersion();
        $this->addNumber();
        $this->addPublisher();
    }

    public function submit(): array
    {
        return array_filter(parent::submit());
    }

    private function addTitle(): void
    {
        $this->text(
            label: 'What is the container title? (if different from source)',
            hint: <<<'EOD'
                e.g., Dutch PHP Conference, YouTube, PHP Architect,
                  Jan's Blog, php.internals, etc.
                EOD,
            name: 'title',
            transform: trim(...),
        );
    }

    private function addPublicationDate(): void
    {
        $this->text(
            label: 'What is the publication date for the container?',
            hint: <<<'EOD'
                e.g., if container is a conference, the date of the presentation;
                  if container is YouTube, the date posted.
                EOD,
            name: 'date',
            transform: trim(...),
        );
    }

    private function addLocation(): void
    {
        $this->text(
            label: 'What is the location for the container?',
            hint: 'e.g., page #, URL, DOI, venue name, etc.',
            name: 'location',
            transform: trim(...),
        );
    }

    private function addContributor(): void
    {
        $this->text(
            label: 'Provide a contributor for the container, if applicable.',
            hint: 'e.g., editor, translator, etc., if not included among authors',
            name: 'contributor',
            transform: trim(...),
        );
    }

    private function addVersion(): void
    {
        $this->text(
            label: 'Provide the container version, if applicable.',
            hint: <<<'EOD'
                The edition or software version of the container.
                  e.g., 2nd edition, e-book edition, version 8.4.2, etc.
                EOD,
            name: 'version',
            transform: trim(...),
        );
    }

    private function addNumber(): void
    {
        $this->text(
            label: 'Provide the container number, if applicable.',
            hint: <<<'EOD'
                The container's location in a sequence. e.g., volume #,
                  issue #, episode #, etc.
                EOD,
            name: 'number',
            transform: trim(...),
        );
    }

    private function addPublisher(): void
    {
        $this->text(
            label: 'Who is the publisher?',
            name: 'publisher',
            transform: trim(...),
        );
    }
}
