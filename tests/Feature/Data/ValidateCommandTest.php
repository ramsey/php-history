<?php

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

describe('data:validate', function () {
    it('returns a success status', function () {
        $this->artisan('data:validate', ['--no-ansi' => true])
            ->expectsOutputToContain('Validating data files...')
            ->assertSuccessful();
    });

    it('warns about a file that does not exist', function () {
        $this->artisan('data:validate', ['--no-ansi' => true, 'file' => ['file/does/not/exist.yaml']])
            ->expectsOutputToContain('Validating data files...')
            ->expectsOutputToContain("\e[3m  not found  \e[0m file/does/not/exist.yaml")
            ->assertSuccessful();
    });

    it('warns about a non-YAML file', function () {
        $this->artisan('data:validate', ['--no-ansi' => true, 'file' => ['tests/Fixtures/invalid-not-yaml.txt']])
            ->expectsOutputToContain('Validating data files...')
            ->expectsOutputToContain("\e[3m  not yaml   \e[0m tests/Fixtures/invalid-not-yaml.txt")
            ->assertSuccessful();
    });

    it('fails when YAML is not an object', function () {
        $this->artisan('data:validate', ['--no-ansi' => true, 'file' => ['tests/Fixtures/invalid-not-object.yml']])
            ->expectsOutputToContain('Validating data files...')
            ->expectsOutputToContain("\e[3m ✖︎ invalid  \e[0m tests/Fixtures/invalid-not-object.yml")
            ->expectsOutputToContain("\e[3m/\e[0m: The data (string) must match the type: object")
            ->assertFailed();
    });

    it('fails when missing required properties', function () {
        $this->artisan(
            'data:validate', [
                '--no-ansi' => true,
                'file' => ['tests/Fixtures/invalid-missing-required.yaml'],
            ])
            ->expectsOutputToContain('Validating data files...')
            ->expectsOutputToContain("\e[3m ✖︎ invalid  \e[0m tests/Fixtures/invalid-missing-required.yaml")
            ->expectsOutputToContain("\e[3m/\e[0m: The required properties (type, summary, date, sources) are missing")
            ->assertFailed();
    });

    it('fails for an invalid type', function () {
        $this->artisan(
            'data:validate', [
                '--no-ansi' => true,
                'file' => ['tests/Fixtures/invalid-type.yaml'],
            ])
            ->expectsOutputToContain('Validating data files...')
            ->expectsOutputToContain("\e[3m ✖︎ invalid  \e[0m tests/Fixtures/invalid-type.yaml")
            ->expectsOutputToContain("\e[3m/type\e[0m: Unexpected type; expected \"event\"")
            ->assertFailed();
    });
});

describe('event validation', function () {
    it('succeeds for an event with minimum properties', function () {
        $this->artisan(
            'data:validate', [
                '--no-ansi' => true,
                'file' => ['tests/Fixtures/valid-event-minimum.yaml'],
            ])
            ->expectsOutputToContain('Validating data files...')
            ->expectsOutputToContain("\e[3m  ✔︎ valid   \e[0m tests/Fixtures/valid-event-minimum.yaml")
            ->assertSuccessful();
    });

    it('succeeds for an event with maximum properties', function () {
        $this->artisan(
            'data:validate', [
                '--no-ansi' => true,
                'file' => ['tests/Fixtures/valid-event-maximum.yaml'],
            ])
            ->expectsOutputToContain('Validating data files...')
            ->expectsOutputToContain("\e[3m  ✔︎ valid   \e[0m tests/Fixtures/valid-event-maximum.yaml")
            ->assertSuccessful();
    });

    it('fails for an event with multiple errors', function () {
        $buffer = new BufferedOutput();
        $result = Artisan::call(
            'data:validate',
            ['--no-ansi' => true, 'file' => ['tests/Fixtures/invalid-event-multiple-errors.yaml']],
            $buffer,
        );
        $output = $buffer->fetch();

        expect($result)->toBe(1)
            ->and($output)->toContain('Validating data files...')
            ->and($output)->toContain("\e[3m ✖︎ invalid  \e[0m tests/Fixtures/invalid-event-multiple-errors.yaml")
            ->and($output)->toContain("\e[3m/summary\e[0m: Maximum string length is 100, found 101")
            ->and($output)->toContain("\e[3m/date\e[0m: The value must be a date in the format YYYY, YYYY-MM, or YYYY-MM-DD")
            ->and($output)->toContain("\e[3m/time\e[0m: The value must be a 24-hour time with timezone offset, e.g. 09:00Z, 16:00-05, 23:59:59+06:30")
            ->and($output)->toContain("\e[3m/endDate\e[0m: The value must be a date in the format YYYY, YYYY-MM, or YYYY-MM-DD")
            ->and($output)->toContain("\e[3m/endTime\e[0m: The value must be a 24-hour time with timezone offset, e.g. 09:00Z, 16:00-05, 23:59:59+06:30")
            ->and($output)->toContain("\e[3m/location/coordinates/latitude\e[0m: The value must be formatted as latitude, e.g. 35.558970")
            ->and($output)->toContain("\e[3m/location/coordinates/longitude\e[0m: The value must be formatted as longitude, e.g. 139.723536")
            ->and($output)->toContain("\e[3m/tags/0\e[0m: Tags may contain only alphanumeric characters, dashes (-), and underscores (_)")
            ->and($output)->toContain("\e[3m/tags/1\e[0m: Tags may contain only alphanumeric characters, dashes (-), and underscores (_)")
            ->and($output)->toContain("\e[3m/tags/2\e[0m: Tags may contain only alphanumeric characters, dashes (-), and underscores (_)")
            ->and($output)->toContain("\e[3m/sources/0/container\e[0m: Array should have at least 1 items, 0 found");
    });
});
