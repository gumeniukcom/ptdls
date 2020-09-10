<?php declare(strict_types=1);

require_once '../vendor/autoload.php';

use Gumeniukcom\Logger\Logger;

$logger = new Logger('pttdls');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');

$dotenv->safeLoad();

$dotenv->required(['REDIS_URL']);

$logger->info("App started");

try {
    $app = new \Gumeniukcom\Tasker\Application($logger);
    $app->run();
} catch (Exception $e) {
    $logger->emergency("exit", ['e' => $e]);
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}


$logger->info("App finished");

