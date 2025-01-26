<?php

declare(strict_types=1);

namespace App;

final class Type
{
    public const string DATA_DIR = __DIR__.'/../data';

    public const string DEFAULT = 'event';

    public const string EVENT_SCHEMA_FILE = __DIR__.'/../schema/event.json';

    public const string EVENT_SCHEMA_ID = 'https://phpc.dev/schema/php-history/event.json';

    public const array TYPE_SCHEMA_MAP = [
        'event' => self::EVENT_SCHEMA_ID,
    ];

    public const array ACCEPTED = [
        'event',
    ];

    private function __construct() {}
}
