<?php

use App\Services\Markdown\HtmlConverter;

describe('HtmlConverter', function () {

    it('converts HTML to Markdown', function () {
        $expected = <<<'EOD'
            # Hello, World!

            Hi, *there*! How are **you**?
            EOD;

        $html = <<<'EOD'
            <h1>Hello, World!</h1>
            <p>Hi, <em>there</em>! How are <strong>you</strong>?</p>
            EOD;

        $converter = new HtmlConverter;

        expect($converter->convert($html))->toBe($expected);
    });

});
