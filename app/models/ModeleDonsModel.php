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

    public static function addModele($nom, $idType)
    {
        $query = "INSERT INTO modeleDons (nom, idTypeDons) VALUES (:nom, :idType)";
        $stmt = Flight::db()->prepare($query);
        return $stmt->execute([':nom' => $nom, ':idType' => (int)$idType]);
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
