<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

date_default_timezone_set('Europe/Paris');

require dirname(__DIR__) . '/vendor/autoload.php';

/*
 * Reproducible ids: without the seed, the yiisoft counter gives 1, 2, 3
 * instead of a timestamp.
 */
\Yiisoft\Html\IdGenerator::$useSeed = false;
