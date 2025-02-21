<?php

use App\Services\Markdown\Converter\WordWrapConverter;
use League\HTMLToMarkdown\Configuration;
use League\HTMLToMarkdown\Element;
use Tests\Mocks\NoOpConverter;

describe('WordWrapConverter', function () {

    it('sets the config on the internal converter', function () {
        $noopConverter = new NoOpConverter([]);
        $wordWrapConverter = new WordWrapConverter($noopConverter);

        $config = new Configuration;
        $wordWrapConverter->setConfig($config);

        expect($noopConverter->config)->toBe($config);
    });

    it('returns the supported tags from the internal converter', function () {
        $noopConverter = new NoOpConverter(['p', 'li']);
        $wordWrapConverter = new WordWrapConverter($noopConverter);

        expect($wordWrapConverter->getSupportedTags())->toBe(['p', 'li']);
    });

    it('wraps the string to the default max line length', function () {
        $expected = <<<'EOD'
            Lorem ipsum odor amet, consectetuer adipiscing elit. Habitasse metus
            ante diam tortor aptent. Neque mattis sociosqu; felis eu montes
            accumsan etiam viverra. Sociosqu morbi eu vestibulum eu praesent fames
            placerat sollicitudin facilisis. Pulvinar aliquam himenaeos sem
            molestie tellus eros mollis. Posuere faucibus per porta; scelerisque
            volutpat placerat cubilia semper. Blandit aliquet pretium eu venenatis
            consequat dictumst laoreet. Cras natoque habitant mattis nascetur
            fringilla finibus semper orci. Adipiscing condimentum aliquet varius
            mi diam sapien ut amet.
            EOD;

        $noopConverter = new NoOpConverter([]);
        $wordWrapConverter = new WordWrapConverter($noopConverter);

        $config = new Configuration;
        $wordWrapConverter->setConfig($config);

        $html = new DOMDocument;
        $html->loadHTMLFile(__DIR__.'/../Fixtures/paragraph.html');
        $node = $html->lastChild->childNodes[1]->childNodes[1] ?? null;
        assert($node instanceof DOMNode);

        $element = new Element($node);

        expect($wordWrapConverter->convert($element))->toBe($expected);
    });

    it('wraps the string to the default max line length with config value', function () {
        $expected = <<<'EOD'
            Lorem ipsum odor amet, consectetuer adipiscing elit. Habitasse
            metus ante diam tortor aptent. Neque mattis sociosqu; felis eu
            montes accumsan etiam viverra. Sociosqu morbi eu vestibulum eu
            praesent fames placerat sollicitudin facilisis. Pulvinar aliquam
            himenaeos sem molestie tellus eros mollis. Posuere faucibus per
            porta; scelerisque volutpat placerat cubilia semper. Blandit
            aliquet pretium eu venenatis consequat dictumst laoreet. Cras
            natoque habitant mattis nascetur fringilla finibus semper orci.
            Adipiscing condimentum aliquet varius mi diam sapien ut amet.
            EOD;

        $noopConverter = new NoOpConverter([]);
        $wordWrapConverter = new WordWrapConverter($noopConverter);

        $config = new Configuration(['max_line_length' => 65]);
        $wordWrapConverter->setConfig($config);

        $html = new DOMDocument;
        $html->loadHTMLFile(__DIR__.'/../Fixtures/paragraph.html');
        $node = $html->lastChild->childNodes[1]->childNodes[1] ?? null;
        assert($node instanceof DOMNode);

        $element = new Element($node);

        expect($wordWrapConverter->convert($element))->toBe($expected);
    });

});
