<?php
/**
 * Manual database reinitialization script
 * This script clears all tables and reloads data from realData.sql
 */

define('ROOT_PATH', __DIR__);

try {
    // Connect to the database directly
    $dsn = 'mysql:host=127.0.0.1;dbname=BNGRC;charset=utf8mb4';
    $user = 'root';
    $pass = '';
    
    $db = new PDO($dsn, $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "[*] Connected to database\n";
    
    // Disable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS=0');
    echo "[*] Disabled foreign key checks\n";
    
    // Truncate all tables
    $tables = [
        'distribution',
        'historiqueDons',
        'achat',
        'besoinsVille',
        'dons',
        'entrepot',
        'modeleDons',
        'typeDons',
        'ville'
    ];
    
    foreach ($tables as $table) {
        $db->exec("TRUNCATE TABLE $table");
        echo "[✓] Truncated $table\n";
    }
    
    // Re-enable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS=1');
    echo "[*] Re-enabled foreign key checks\n";
    
    // Load and execute SQL
    $realDataPath = ROOT_PATH . '/bdd/realData.sql';
    if (!file_exists($realDataPath)) {
        throw new Exception('realData.sql not found');
    }
    
    $sql = file_get_contents($realDataPath);
    
    // Split statements by semicolon and execute each
    $statements = preg_split('/;(?!["\'])/', $sql);
    $count = 0;
    
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        
        // Skip empty lines and comments
        if (empty($stmt) || preg_match('/^\s*--/', $stmt) || preg_match('/^\s*\/\*/', $stmt)) {
            continue;
        }
        
        // Skip lines that start with comment
        $lines = explode("\n", $stmt);
        $firstLine = trim($lines[0]);
        if (preg_match('/^--/', $firstLine)) {
            continue;
        }
        
        try {
            $db->exec($stmt);
            $count++;
            echo "[✓] Executed statement $count\n";
        } catch (PDOException $e) {
            echo "[!] Error executing statement: " . $e->getMessage() . "\n";
            echo "Statement: " . substr($stmt, 0, 100) . "...\n";
        }
    }
    
    echo "\n[SUCCESS] Database reinitialized! Executed $count statements.\n";
    
    // Verify data
    $result = $db->query('SELECT COUNT(*) as dons_count FROM dons');
    $data = $result->fetch(PDO::FETCH_ASSOC);
    echo "[INFO] Total donations in dons table: " . $data['dons_count'] . "\n";
    
    $result = $db->query('SELECT COUNT(*) as types_count FROM typeDons');
    $data = $result->fetch(PDO::FETCH_ASSOC);
    echo "[INFO] Total types: " . $data['types_count'] . "\n";
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
?>
