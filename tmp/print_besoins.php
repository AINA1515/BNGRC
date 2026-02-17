<?php
require __DIR__ . '/../vendor/autoload.php';

// Provide minimal constants expected by app/bootstrap when running from CLI
if (!defined('BASE_URL')) define('BASE_URL', '');
if (!defined('ROOT_PATH')) define('ROOT_PATH', rtrim(dirname(__DIR__), '/'));

require __DIR__ . '/../app/config/bootstrap.php';

use app\models\BesoinVilleModel;

$besoins = BesoinVilleModel::getEnrichedBesoinsForDashboard();
header('Content-Type: application/json');
echo json_encode($besoins, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
