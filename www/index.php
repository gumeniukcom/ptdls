<?php declare(strict_types=1);

require_once '../vendor/autoload.php';

use Gumeniukcom\Logger\Logger;

$logger = new Logger('pttdls');

$logger->info("App started");

echo "Hello world!";

$logger->info("App finished");

