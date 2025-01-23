<?php

test('validate command', function () {
    $this->artisan('data:validate')->assertExitCode(0);
});
