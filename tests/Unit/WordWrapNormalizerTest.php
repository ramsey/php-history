<?php

use App\Services\Markdown\HtmlConverter;
use App\Services\Markdown\WordWrapNormalizer;
use League\CommonMark\CommonMarkConverter;

describe('WordWrapNormalizer', function () {

    it('wraps Markdown text', function () {
        $markdown = <<<'EOD'
            Lorem ipsum odor amet, consectetuer adipiscing elit. Habitasse metus ante diam tortor aptent. Neque mattis sociosqu; felis eu montes accumsan etiam viverra. Sociosqu morbi eu vestibulum eu praesent fames placerat sollicitudin facilisis. Pulvinar aliquam himenaeos sem molestie tellus eros mollis. Posuere faucibus per porta; scelerisque volutpat placerat cubilia semper. Blandit aliquet pretium eu venenatis consequat dictumst laoreet. Cras natoque habitant mattis nascetur fringilla finibus semper orci. Adipiscing condimentum aliquet varius mi diam sapien ut amet.

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
            EOD;

        $expected = <<<'EOD'
            Lorem ipsum odor amet, consectetuer adipiscing elit. Habitasse metus ante
            diam tortor aptent. Neque mattis sociosqu; felis eu montes accumsan etiam
            viverra. Sociosqu morbi eu vestibulum eu praesent fames placerat
            sollicitudin facilisis. Pulvinar aliquam himenaeos sem molestie tellus eros
            mollis. Posuere faucibus per porta; scelerisque volutpat placerat cubilia
            semper. Blandit aliquet pretium eu venenatis consequat dictumst laoreet.
            Cras natoque habitant mattis nascetur fringilla finibus semper orci.
            Adipiscing condimentum aliquet varius mi diam sapien ut amet.

            - Fringilla egestas sociosqu interdum sapien lectus porttitor curabitur nulla.
              Dis nam curabitur scelerisque; lectus varius ultricies.
            - Porttitor laoreet ultrices facilisi imperdiet sed faucibus ante.
              - Convallis taciti facilisis natoque conubia litora nullam mollis. Massa
                turpis ex magna dignissim justo mauris quam id vel.
                - Convallis taciti facilisis natoque conubia litora nullam mollis. Massa
                  turpis ex magna dignissim justo mauris quam id vel.
                - Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus
                  lectus ex dolor neque aptent amet.
              - Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus
                lectus ex dolor neque aptent amet.

            1. Fringilla egestas sociosqu interdum sapien lectus porttitor curabitur nulla.
               Dis nam curabitur scelerisque; lectus varius ultricies.
            2. Porttitor laoreet ultrices facilisi imperdiet sed faucibus ante.
               1. Convallis taciti facilisis natoque conubia litora nullam mollis. Massa
                  turpis ex magna dignissim justo mauris quam id vel.
                  1. Convallis taciti facilisis natoque conubia litora nullam mollis. Massa
                     turpis ex magna dignissim justo mauris quam id vel.
                  2. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus
                     lectus ex dolor neque aptent amet.
               2. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus
                  lectus ex dolor neque aptent amet.
                  1. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus
                     lectus ex dolor neque aptent amet.
                  2. Platea nam vehicula sed proin felis. Class tempus adipiscing vivamus
                     lectus ex dolor neque aptent amet.
            EOD;

        $normalizer = new WordWrapNormalizer(new CommonMarkConverter, new HtmlConverter);

        expect($normalizer->convert($markdown))->toBe($expected);
    });

});
