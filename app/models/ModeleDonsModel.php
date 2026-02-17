<?php

namespace app\models;

use Flight;

class ModeleDonsModel
{
    /**
     * Get all donation models.
     *
     * @return array
     */
    public static function getAllModeles()
    {
        $query = "SELECT * FROM modeleDons";
        return Flight::db()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get a donation model by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getModeleById($id)
    {
        $query = "SELECT * FROM modeleDons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':id' => (int)$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /**
     * Get donation models by type.
     *
     * @param int $idTypeDons
     * @return array
     */
    public static function getModelesByType($idTypeDons)
    {
        $query = "SELECT * FROM modeleDons WHERE idTypeDons = :idTypeDons";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':idTypeDons' => (int)$idTypeDons]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
