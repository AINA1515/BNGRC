<?php

namespace app\models;

use Flight;

class EntrepotModel
{
    /**
     * Add stock for a modele. If row exists, increment quantity.
     */
    public static function addStock($idModeleDons, $quantite)
    {
        $idModeleDons = (int)$idModeleDons;
        $quantite = (int)$quantite;
        if ($idModeleDons <= 0 || $quantite <= 0) return false;
        $db = Flight::db();
        // try update
        $stmt = $db->prepare('UPDATE entrepot SET quantite = quantite + :q WHERE idModeleDons = :id');
        $ok = $stmt->execute([':q' => $quantite, ':id' => $idModeleDons]);
        if ($ok && $stmt->rowCount() > 0) return true;
        // insert
        $stmt2 = $db->prepare('INSERT INTO entrepot (idModeleDons, quantite) VALUES (:id, :q)');
        return $stmt2->execute([':id' => $idModeleDons, ':q' => $quantite]);
    }

    /**
     * Remove stock for a modele, return true if removed (may set to zero if insufficient).
     */
    public static function removeStock($idModeleDons, $quantite)
    {
        $idModeleDons = (int)$idModeleDons;
        $quantite = (int)$quantite;
        if ($idModeleDons <= 0 || $quantite <= 0) return false;
        $db = Flight::db();
        // fetch current
        $stmt = $db->prepare('SELECT quantite FROM entrepot WHERE idModeleDons = :id FOR UPDATE');
        $stmt->execute([':id' => $idModeleDons]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return false;
        $current = (int)$row['quantite'];
        $new = max(0, $current - $quantite);
        $u = $db->prepare('UPDATE entrepot SET quantite = :q WHERE idModeleDons = :id');
        return $u->execute([':q' => $new, ':id' => $idModeleDons]);
    }

    public static function getStockByModele($idModeleDons)
    {
        $stmt = Flight::db()->prepare('SELECT * FROM entrepot WHERE idModeleDons = :id');
        $stmt->execute([':id' => (int)$idModeleDons]);
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $r === false ? null : $r;
    }

    public static function getAllStock()
    {
        return Flight::db()->query('SELECT e.*, m.nom as modeleNom FROM entrepot e LEFT JOIN modeleDons m ON m.id = e.idModeleDons')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function setStock($idModeleDons, $quantite)
    {
        $id = (int)$idModeleDons;
        $q = (int)$quantite;
        if ($id <= 0) return false;
        $db = Flight::db();
        // check existing
        $sel = $db->prepare('SELECT id FROM entrepot WHERE idModeleDons = :id');
        $sel->execute([':id' => $id]);
        $row = $sel->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $u = $db->prepare('UPDATE entrepot SET quantite = :q WHERE idModeleDons = :id');
            return $u->execute([':q' => $q, ':id' => $id]);
        }
        $ins = $db->prepare('INSERT INTO entrepot (idModeleDons, quantite) VALUES (:id, :q)');
        return $ins->execute([':id' => $id, ':q' => $q]);
    }
}
