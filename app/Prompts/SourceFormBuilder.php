<?php

declare(strict_types=1);

namespace App\Prompts;

use Laravel\Prompts\FormBuilder;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\note;
use function Laravel\Prompts\text;

final class SourceFormBuilder extends FormBuilder
{
    public function __construct()
    {
        $this->addTitle();
        $this->addAuthors();
        $this->addContainers();
    }

    public function submit(): array
    {
        return array_filter(parent::submit());
    }

    private function addTitle(): void
    {
        $this->text(
            label: 'What is the title of the source?',
            required: true,
            hint: "This could be the title of a blog post, web page, article,\n  chapter, book, email subject, etc.",
            name: 'title',
            transform: trim(...),
        );
    }

    private function addAuthors(): void
    {
        $this->add(
            step: function (): array {
                $authors = [];

                do {
                    $author = text(
                        label: 'What is the '.$this->ordinal(count($authors) + 1).' author\'s name?',
                        placeholder: "Provide the author's name. e.g., Rasmus Lerdorf, KOYAMA\n"
                            .'Tetsuji, Zend Technologies, Inc., etc.',
                        hint: 'Leave blank if there are no more authors.',
                        transform: trim(...),
                    );

                    if ($author !== '') {
                        $authors[] = $author;
                    }
                } while ($author !== '');

                return $authors;
            },
            name: 'authors',
        );
    }

    private function addContainers(): void
    {
        $this->add(
            step: function (): array {
                $containers = [];

                note(<<<'EOD'
                    A source may be in one or more "containers," or it may be self-
                    contained. For example, a book is self-contained, but a record-
                    ing of a conference presentation watched on YouTube might have
                    two containers: the first container is the conference where the
                    presentation took place, and the second container is YouTube.

                    The inspiration for this style of citing sources comes from the
                    Modern Language Association (MLA) style guide. The MLA provides
                    a concise and straightforward approach to gathering and organiz-
                    ing source information, in much the same way a programmer might
                    think about a data model for sources, so we use it here to cap-
                    ture this information. To find out more about source containers,
                    see https://style.mla.org/works-cited/works-cited-a-quick-guide
                    EOD);

                do {
                    $container = (new ContainerFormBuilder)->submit();

                    if ($container !== []) {
                        $containers[] = $container;
                    }
                } while (confirm(label: 'Do you want to add another container?', default: false));

                return $containers;
            },
            name: 'containers',
        );
    }

    private function ordinal(int $number): string
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number.'th';
        }

        return $number.$ends[$number % 10];
    }
}
