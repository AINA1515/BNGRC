<?php

namespace app\models;

use Flight;

class ModeleDonsModel
{
    public static function getAllModeles()
    {
        $query = "SELECT * FROM modeleDons";
        return Flight::db()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Add a new donation model. prixUnitaire is required by the DB schema, default to 0.00 when omitted.
     *
     * @param string $nom
     * @param int $idType
     * @param float|null $prixUnitaire
     * @return bool
     */
    public static function addModele($nom, $idType, $prixUnitaire = 0.00)
    {
        $query = "INSERT INTO modeleDons (nom, prixUnitaire, idTypeDons) VALUES (:nom, :prixUnitaire, :idType)";
        $stmt = Flight::db()->prepare($query);
        return $stmt->execute([
            ':nom' => $nom,
            ':prixUnitaire' => $prixUnitaire === null ? 0.00 : (float)$prixUnitaire,
            ':idType' => (int)$idType
        ]);
    }

    public static function getModeleById($id)
    {
        $query = "SELECT * FROM modeleDons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':id' => (int)$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public static function getModelesByType($idTypeDons)
    {
        $query = "SELECT * FROM modeleDons WHERE idTypeDons = :idTypeDons";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':idTypeDons' => (int)$idTypeDons]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
