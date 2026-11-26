<?php

define('DATABASE_DSN', getenv('PRESS_DSN') ?: 'sqlite:' . __DIR__ . '/../../var/press-demo.sqlite');
define('DATABASE_USERNAME', getenv('PRESS_DB_USER') ?: '');
define('DATABASE_PASSWORD', getenv('PRESS_DB_PASSWORD') ?: '');
