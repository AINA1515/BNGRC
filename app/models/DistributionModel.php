<?php

namespace app\models;

use Flight;

class DistributionModel
{
    /**
     * Create a distribution record.
     *
     * @param array $data keys: idBesoins, idVille, date_, quantiteBesoinDepart, quantiteBesoinRestant, quantiteDonsInitiale, quantiteDonsDistribue, prixUnitaire
     * @return bool
     */
    public static function createDistribution(array $data)
    {
        $query = "INSERT INTO distribution (idBesoins, idVille, date_, quantiteBesoinDepart, quantiteBesoinRestant, quantiteDonsInitiale, quantiteDonsDistribue, prixUnitaire) VALUES (:idBesoins, :idVille, :date_, :qDepart, :qRestant, :qDonsInit, :qDonsDistrib, :prix)";
        $stmt = Flight::db()->prepare($query);
        return $stmt->execute([
            ':idBesoins' => (int)($data['idBesoins'] ?? 0),
            ':idVille' => (int)($data['idVille'] ?? 0),
            ':date_' => $data['date_'] ?? date('Y-m-d H:i:s'),
            ':qDepart' => (int)($data['quantiteBesoinDepart'] ?? 0),
            ':qRestant' => (int)($data['quantiteBesoinRestant'] ?? 0),
            ':qDonsInit' => (int)($data['quantiteDonsInitiale'] ?? 0),
            ':qDonsDistrib' => (int)($data['quantiteDonsDistribue'] ?? 0),
            ':prix' => isset($data['prixUnitaire']) ? $data['prixUnitaire'] : null,
        ]);
    }

    /**
     * Fetch recent distributions for display.
     *
     * @return array
     */
    public static function getRecentDistributions($limit = 50)
    {
        $query = "SELECT d.*, b.idVille as besoinVilleId, b.quantite as besoinQuantite, md.nom as modeleNom, v.nom as villeNom FROM distribution d LEFT JOIN besoinsVille b ON b.id = d.idBesoins LEFT JOIN ville v ON v.id = d.idVille LEFT JOIN modeleDons md ON md.id = b.idModeleDons ORDER BY d.date_ DESC LIMIT :limit";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get a distribution by id
     * @param int $id
     * @return array|null
     */
    public static function getById($id)
    {
        $query = "SELECT * FROM distribution WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':id' => (int)$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /**
     * Update a distribution row partially. $fields is associative array of column => value.
     */
    public static function updateDistribution($id, array $fields)
    {
        if (empty($fields)) return false;
        $sets = [];
        $params = [':id' => (int)$id];
        foreach ($fields as $col => $val) {
            $sets[] = "`$col` = :$col";
            $params[":$col"] = $val;
        }
        $sql = "UPDATE distribution SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute($params);
    }
}
