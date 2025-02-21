<?php

namespace Tests\Mocks;

use League\HTMLToMarkdown\Configuration;
use League\HTMLToMarkdown\ConfigurationAwareInterface;
use League\HTMLToMarkdown\Converter\ConverterInterface;
use League\HTMLToMarkdown\ElementInterface;

class NoOpConverter implements ConfigurationAwareInterface, ConverterInterface
{
    public ?Configuration $config = null;

    /**
     * @param  list<string>  $supportedTags
     */
    public function __construct(public readonly array $supportedTags) {}

    public function setConfig(Configuration $config): void
    {
        $this->config = $config;
    }

    public function convert(ElementInterface $element): string
    {
        return $element->getValue();
    }

    public function getSupportedTags(): array
    {
        return $this->supportedTags;
    }
}
