<?php

declare(strict_types=1);

namespace App\Services\Markdown;

use League\CommonMark\ConverterInterface;
use League\HTMLToMarkdown\HtmlConverterInterface;

/**
 * Wraps Markdown content to a maximum line length, for easier readability.
 */
readonly class WordWrapNormalizer implements Normalizer
{
    public function __construct(
        private ConverterInterface $markdownConverter,
        private HtmlConverterInterface $htmlConverter,
    ) {}

    public function convert(string $input): string
    {
        // Convert to HTML.
        $html = $this->markdownConverter->convert($input);

        // Convert back to Markdown.
        return $this->htmlConverter->convert((string) $html);
    }
}
