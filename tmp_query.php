<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=BNGRC;charset=utf8mb4','root','');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $count = $pdo->query('SELECT COUNT(*) FROM vue_dons_par_ville')->fetchColumn();
    echo 'count=' . $count . PHP_EOL;
    $stmt = $pdo->query('SELECT * FROM vue_dons_par_ville LIMIT 5');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) { var_export($r); echo PHP_EOL; }
} catch (Exception $e) { echo 'Error: ' . $e->getMessage() . PHP_EOL; }
