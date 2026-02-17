<?php
// One-off debug script to inspect DonsModel::getDonationById
// set project root
$root = dirname(__DIR__);
chdir($root);
require $root . '/vendor/autoload.php';

// Initialize Flight app and config
$app = \Flight::app();
$ds = DIRECTORY_SEPARATOR;
$config = require $root . '/app/config/config.php';
// register services (database, etc.)
require $root . '/app/config/services.php';

try {
    $id = 4;
    $don = \app\models\DonsModel::getDonationById($id);
    echo json_encode(['id' => $id, 'don' => $don], JSON_PRETTY_PRINT) . PHP_EOL;
} catch (\Throwable $e) {
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]) . PHP_EOL;
}
