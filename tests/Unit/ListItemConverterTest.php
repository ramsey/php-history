<?php

use App\Services\Markdown\Converter\ListItemConverter;
use App\Services\Markdown\Converter\WordWrapConverter;
use League\HTMLToMarkdown\Configuration;
use League\HTMLToMarkdown\Converter\ListBlockConverter;
use League\HTMLToMarkdown\Environment;
use League\HTMLToMarkdown\HtmlConverter;

describe('ListItemConverter', function () {

    it('sets the config on the internal converter', function () {
        $converter = new ListItemConverter;

        $config = new Configuration;
        $converter->setConfig($config);

        expect($converter->config)->toBe($config);
    });

    it('returns the supported tags', function () {
        $converter = new ListItemConverter;

        expect($converter->getSupportedTags())->toBe(['li']);
    });

    it('indents the list items properly', function () {
        $expected = <<<'EOD'
            - Fringilla egestas sociosqu interdum sapien lectus porttitor curabitur nulla. Dis nam curabitur scelerisque; lectus varius ultricies.
            - Porttitor laoreet ultrices facilisi imperdiet sed faucibus ante.
              - Convallis taciti facilisis natoque conubia litora nullam mollis. Massa turpis ex magna dignissim justo mauris quam id vel.
                - Convallis taciti facilisis natoque conubia litora nullam mollis. Massa turpis ex magna dignissim justo mauris quam id vel.
                - Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
              - Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.

            1. Fringilla egestas sociosqu interdum sapien lectus porttitor curabitur nulla. Dis nam curabitur scelerisque; lectus varius ultricies.
            2. Porttitor laoreet ultrices facilisi imperdiet sed faucibus ante.
               1. Convallis taciti facilisis natoque conubia litora nullam mollis. Massa turpis ex magna dignissim justo mauris quam id vel.
                  1. Convallis taciti facilisis natoque conubia litora nullam mollis. Massa turpis ex magna dignissim justo mauris quam id vel.
                  2. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
               2. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  1. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  2. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  3. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  4. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  5. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  6. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  7. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  8. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  9. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  10. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
                  11. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus lectus ex dolor neque aptent amet.
            EOD;

        $environment = Environment::createDefaultEnvironment();
        $environment->addConverter(new ListBlockConverter);
        $environment->addConverter(new ListItemConverter);

        $converter = new HtmlConverter($environment);

        expect($converter->convert(file_get_contents(__DIR__.'/../Fixtures/list-items.html')))->toBe($expected);
    });

    it('wraps the list items as expected', function () {
        $expected = <<<'EOD'
            - Fringilla egestas sociosqu interdum sapien lectus porttitor curabitur
              nulla. Dis nam curabitur scelerisque; lectus varius ultricies.
            - Porttitor laoreet ultrices facilisi imperdiet sed faucibus ante.
              - Convallis taciti facilisis natoque conubia litora nullam mollis.
                Massa turpis ex magna dignissim justo mauris quam id vel.
                - Convallis taciti facilisis natoque conubia litora nullam mollis.
                  Massa turpis ex magna dignissim justo mauris quam id vel.
                - Platea nam vehicula sed proin felis. Class tempus adipiscing
                  vivamus lectus ex dolor neque aptent amet.
              - Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus
                lectus ex dolor neque aptent amet.

            1. Fringilla egestas sociosqu interdum sapien lectus porttitor curabitur
               nulla. Dis nam curabitur scelerisque; lectus varius ultricies.
            2. Porttitor laoreet ultrices facilisi imperdiet sed faucibus ante.
               1. Convallis taciti facilisis natoque conubia litora nullam mollis.
                  Massa turpis ex magna dignissim justo mauris quam id vel.
                  1. Convallis taciti facilisis natoque conubia litora nullam mollis.
                     Massa turpis ex magna dignissim justo mauris quam id vel.
                  2. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
               2. Platea nam vehicula sed proin felis. Class tempus adipiscing
               vivamus
                  lectus ex dolor neque aptent amet.
                  1. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  2. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  3. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  4. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  5. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  6. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  7. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  8. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  9. Platea nam vehicula sed proin felis. Class tempus adipiscing
                     vivamus lectus ex dolor neque aptent amet.
                  10. Platea nam vehicula sed proin felis. Class tempus adipiscing
                      vivamus lectus ex dolor neque aptent amet.
                  11. Platea nam vehicula sed proin felis. Class tempus adipiscing
                      vivamus lectus ex dolor neque aptent amet.
            EOD;

        $environment = Environment::createDefaultEnvironment(['max_line_length' => 70]);
        $environment->addConverter(new ListBlockConverter);
        $environment->addConverter(new WordWrapConverter(new ListItemConverter));

        $converter = new HtmlConverter($environment);

        expect($converter->convert(file_get_contents(__DIR__.'/../Fixtures/list-items.html')))->toBe($expected);
    });

});
