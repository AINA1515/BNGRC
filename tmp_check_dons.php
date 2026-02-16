<?php
require __DIR__ . '/vendor/autoload.php';

// définir le séparateur de dossier attendu par config.php
$ds = DIRECTORY_SEPARATOR;

// Initialiser Flight
\Flight::init();

// Charger la configuration (retourne le tableau)
$config = require __DIR__ . '/app/config/config.php';

// Charger le modèle directement (le namespace app\models n'est pas mappé par composer dans ce projet)
require_once __DIR__ . '/app/models/VueDonsParVilleModel.php';

// Construire le DSN (comme dans services.php)
$dsn = 'mysql:host=' . ($config['database']['host'] ?? '127.0.0.1') . ';dbname=' . ($config['database']['dbname'] ?? '') . ';charset=utf8mb4';

// Enregistrer le service db via Flight::register pour que Flight::db() existe
// Préférence pour PdoWrapper si disponible
if (class_exists('\flight\database\PdoWrapper')) {
    // Register the existing PdoWrapper class so Flight::db() is mapped
    \Flight::register('db', \flight\database\PdoWrapper::class, [ $dsn, $config['database']['user'] ?? null, $config['database']['password'] ?? null ]);
} else {
    // fallback to native PDO
    // fallback: define a small service wrapper that wraps PDO and register it by class name
    class SimplePdoService {
        private $pdo;
        public function __construct($dsn, $user = null, $pass = null) {
            $this->pdo = new \PDO($dsn, $user, $pass);
            // set error mode to exceptions to mimic PdoWrapper
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        public function query($sql) { return $this->pdo->query($sql); }
        public function prepare($sql) { return $this->pdo->prepare($sql); }
    }

    \Flight::register('db', SimplePdoService::class, [ $dsn, $config['database']['user'] ?? null, $config['database']['password'] ?? null ]);
}

try {
    // appeler directement la méthode du modèle
    $res = \app\models\VueDonsParVilleModel::getView();
    if (is_array($res)) {
        echo "Count: " . count($res) . "\n";
        foreach (array_slice($res, 0, 5) as $r) {
            var_dump($r);
        }
    } else {
        var_dump($res);
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
