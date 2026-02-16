<?php

namespace app\models;

use Flight;

class DonsModel
{
    /**
     * Get all donations.
     *
     * @return array
     */
    public static function getAllDonations()
    {
        // Example query, replace with your actual database logic
        $query = "SELECT * FROM dons";
        return Flight::db()->query($query)->fetchAll();
    }

    /**
     * Get a donation by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getDonationById($id)
    {
        $query = "SELECT * FROM dons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getDonationByName($name)
    {
        $query = "SELECT * FROM dons WHERE nom = :name";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getDonationsByIdType($idType)
    {
        $query = "SELECT * FROM dons WHERE idTypeDons = :idType";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':idType', $idType, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public static function addDonation($name,$idTypeDons)
    {
        $query = "INSERT INTO dons (nom, idTypeDons) VALUES (:name, :idTypeDons)";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':idTypeDons', $idTypeDons, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function updateDonation($id, $name, $idTypeDons)
    {
        $query = "UPDATE dons SET nom = :name, idTypeDons = :idTypeDons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':idTypeDons', $idTypeDons, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function deleteDonation($id)
    {
        $query = "DELETE FROM dons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
