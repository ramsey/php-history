<?php

/**
 * ListItemConverter, from league/html-to-markdown
 *
 * Copyright (c) 2015 Colin O'Dell; Originally created by Nick Cernis
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Services\Markdown\Converter;

use League\HTMLToMarkdown\Coerce;
use League\HTMLToMarkdown\Configuration;
use League\HTMLToMarkdown\ConfigurationAwareInterface;
use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

use function explode;
use function implode;
use function intval;
use function trim;

/**
 * This copies ListItemConverter almost verbatim from league/html-to-markdown.
 * The primary difference is the use of two spaces (instead of four) to the
 * nested list items. A better option would be to change this to an option named
 * list_item_indentation (or something like that) and contribute it upstream.
 */
class ListItemConverter implements ConfigurationAwareInterface, ConverterInterface
{
    private const int LIST_ITEM_INDENTATION = 2;

    private(set)

 public Configuration $config;

    private ?string $listItemStyle = null;

    public function setConfig(Configuration $config): void
    {
        $this->config = $config;
    }

    public function convert(ElementInterface $element): string
    {
        // If parent is an ol, use numbers, otherwise, use dashes
        $listType = ($parent = $element->getParent()) ? $parent->getTagName() : 'ul';

        $number = 0;
        if ($listType === 'ol' && ($parent = $element->getParent()) && ($start = intval($parent->getAttribute('start')))) {
            $number = $start + $element->getSiblingPosition() - 1;
        } elseif ($listType === 'ol') {
            $number = $element->getSiblingPosition();
        }

        $listItemIndentation = match (true) {
            $number > 0 && $number < 10 => self::LIST_ITEM_INDENTATION + 1,
            $number >= 10 && $number < 100 => self::LIST_ITEM_INDENTATION + 2,
            $number >= 100 && $number < 1000 => self::LIST_ITEM_INDENTATION + 3,
            default => self::LIST_ITEM_INDENTATION,
        };

        // Add spaces to start for nested list items
        $level = $element->getListItemLevel();

        $value = trim(
            implode(
                "\n".str_repeat(' ', $listItemIndentation),
                explode("\n", trim($element->getValue())),
            ),
        );

        // If list item is the first in a nested list, add a newline before it
        $prefix = '';
        if ($level > 0 && $element->getSiblingPosition() === 1) {
            $prefix = "\n";
        }

        if ($listType === 'ul') {
            $listItemStyle = Coerce::toString($this->config->getOption('list_item_style', '-'));
            $listItemStyleAlternate = Coerce::toString($this->config->getOption('list_item_style_alternate', ''));
            if (! isset($this->listItemStyle)) {
                $this->listItemStyle = $listItemStyleAlternate ?: $listItemStyle;
            }

            if ($listItemStyleAlternate && $level === 0 && $element->getSiblingPosition() === 1) {
                $this->listItemStyle = $this->listItemStyle === $listItemStyle ? $listItemStyleAlternate : $listItemStyle;
            }

            return $prefix.$this->listItemStyle.' '.$value."\n";
        }

        return $prefix.$number.'. '.$value."\n";
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['li'];
    }
}
