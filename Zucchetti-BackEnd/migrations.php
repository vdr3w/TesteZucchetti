<?php
// migrations.php

use Doctrine\Migrations\Configuration\Migration\PhpFile;

return new PhpFile(__DIR__ . '/migrations-config.php', [
    'migrations_paths' => [
        'MyProject\Migrations' => __DIR__ . '/migrations',
    ],
    'all_or_nothing' => true,
    'check_database_platform' => true,
]);
