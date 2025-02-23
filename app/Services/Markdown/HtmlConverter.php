<?php

declare(strict_types=1);

namespace App\Services\Markdown;

use App\Services\Markdown\Converter\ListItemConverter;
use App\Services\Markdown\Converter\WordWrapConverter;
use League\HTMLToMarkdown\Converter\ParagraphConverter;
use League\HTMLToMarkdown\Converter\TableConverter;
use League\HTMLToMarkdown\Environment;
use League\HTMLToMarkdown\HtmlConverter as LeagueHtmlConverter;
use League\HTMLToMarkdown\HtmlConverterInterface;

/**
 * Converts HTML to Markdown.
 */
readonly class HtmlConverter implements HtmlConverterInterface
{
    private HtmlConverterInterface $htmlConverter;

    public function __construct()
    {
        $environment = Environment::createDefaultEnvironment([
            'header_style' => 'atx',
            'suppress_errors' => true,
            'strip_tags' => false,
            'strip_placeholder_links' => true,
            'bold_style' => '**',
            'italic_style' => '*',
            'remove_nodes' => '',
            'hard_break' => false,
            'list_item_style' => '-',
            'preserve_comments' => true,
            'use_autolinks' => true,
            'table_pipe_escape' => '\|',
            'table_caption_side' => 'top',
            'max_line_length' => 76,
        ]);

        $environment->addConverter(new TableConverter);
        $environment->addConverter(new WordWrapConverter(new ParagraphConverter));
        $environment->addConverter(new WordWrapConverter(new ListItemConverter));

        $this->htmlConverter = new LeagueHtmlConverter($environment);
    }

    public function convert(string $html): string
    {
        return $this->htmlConverter->convert($html);
    }
}
