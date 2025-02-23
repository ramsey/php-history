<?php

namespace App\Services\Markdown;

interface Normalizer
{
    /**
     * Converts Markdown to HTML and back to Markdown again to normalize the
     * Markdown format.
     */
    public function convert(string $input): string;
}
