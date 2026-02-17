<?php
require __DIR__ . '/../vendor/autoload.php';
if (!defined('BASE_URL')) define('BASE_URL', '');
if (!defined('ROOT_PATH')) define('ROOT_PATH', rtrim(dirname(__DIR__), '/'));
require __DIR__ . '/../app/config/bootstrap.php';

try {
    $sim = \app\models\BesoinVilleModel::simulateAllocation();
    header('Content-Type: application/json');
    echo json_encode($sim, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
