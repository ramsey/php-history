<?php

declare(strict_types=1);

namespace App\Services\Markdown\Converter;

use League\HTMLToMarkdown\Configuration;
use League\HTMLToMarkdown\ConfigurationAwareInterface;
use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;
use ReflectionProperty;

use function trim;
use function wordwrap;

/**
 * The word wrap converter wraps the value of an element to the number of
 * characters per line, as defined by the max_line_length Configuration
 * parameter (provided via `setConfig()`), before passing it to the provided
 * converter for further processing.
 */
class WordWrapConverter implements ConfigurationAwareInterface, ConverterInterface
{
    private const int DEFAULT_MAX_LINE_LENGTH = 70;

    private const int LIST_ITEM_INDENTATION = 2;

    private Configuration $config;

    private int $maxLineLength = self::DEFAULT_MAX_LINE_LENGTH;

    public function __construct(private readonly ConverterInterface $converter) {}

    public function setConfig(Configuration $config): void
    {
        $this->config = $config;
        $this->maxLineLength = $this->config->getOption('max_line_length', $this->maxLineLength);

        if ($this->converter instanceof ConfigurationAwareInterface) {
            $this->converter->setConfig($this->config);
        }
    }

    public function convert(ElementInterface $element): string
    {
        $maxLineLength = $this->maxLineLength;

        if ($element->getListItemLevel() > 0) {
            // TODO: Need to account for ordered lists...
            $maxLineLength = $this->maxLineLength - (self::LIST_ITEM_INDENTATION * $element->getListItemLevel());
        }

        // Get the element value, call wordwrap() on it, store the wrapped
        // value back to the DOMNode that's on the element, and process the
        // element using the converter provided.
        $reflectedNode = new ReflectionProperty($element, 'node');
        $node = $reflectedNode->getValue($element);
        $node->nodeValue = implode(
            "\n",
            array_map(
                rtrim(...),
                explode(
                    "\n",
                    wordwrap(
                        trim($element->getValue()),
                        $maxLineLength,
                    ),
                ),
            ),
        );

        return $this->converter->convert($element);
    }

    public function getSupportedTags(): array
    {
        return $this->converter->getSupportedTags();
    }
}
