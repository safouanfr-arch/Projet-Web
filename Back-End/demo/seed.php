<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$directory = __DIR__ . '/../var';
if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
    fwrite(STDERR, "Could not create the demo directory.\n");
    exit(1);
}

$file = $directory . '/press-demo.sqlite';
if (is_file($file)) {
    fwrite(STDERR, "The demo database already exists. Move it aside to regenerate it.\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $file, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec(file_get_contents(__DIR__ . '/schema.sql'));
echo "SQLite demo ready.\n";
