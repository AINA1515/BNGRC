<?php

namespace app\models;

use Flight;

class HistoriqueDonsModel
{
    /**
     * Get all donation history records.
     *
     * @return array
     */
    public static function getAllHistoriqueDons()
    {
        $query = "SELECT * FROM historique_dons";
        return Flight::db()->query($query)->fetchAll();
    }

    /**
     * Get donation history by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getHistoriqueDonsById($id)
    {
        $query = "SELECT * FROM historique_dons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get donation history by donation ID.
     *
     * @param int $idDOns
     * @return array|null
     */
    public static function getHistoriqueDonsByIdDons($idDons)
    {
        $query = "SELECT * FROM historique_dons WHERE idDons = :idDons";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':idDons', $idDons, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get donation history by city ID.
     *
     * @param int $idVille
     * @return array|null
     */
    public static function getHistoriqueDonsByIdVille($idVille)
    {
        $query = "SELECT * FROM historique_dons WHERE idVille = :idVille";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':idVille', $idVille, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Add a new donation history record.
     *
     * @param int $idBesoin
     * @param int $idDons
     * @param int $quantite
     * @return bool
     */
    public static function addHistoriqueDons($idDons, $date_, $idVille)
    {
        $query = "INSERT INTO historique_dons (idDons, date_, idVille) VALUES (:idDons, :date_, :idVille)";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':idDons', $idDons, \PDO::PARAM_INT);
        $stmt->bindParam(':date_', $date_, \PDO::PARAM_STR);
        $stmt->bindParam(':idVille', $idVille, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Update a donation history record.
     *
     * @param int $id
     * @param int $idBesoin
     * @param int $idDons
     * @param int $quantite
     * @return bool
     */
    public static function updateHistoriqueDons($id, $idDons, $date_, $idVille)
    {
        $query = "UPDATE historique_dons SET idDons = :idDons, date_ = :date_, idVille = :idVille WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':idDons', $idDons, \PDO::PARAM_INT);
        $stmt->bindParam(':date_', $date_, \PDO::PARAM_STR);
        $stmt->bindParam(':idVille', $idVille, \PDO::PARAM_INT);
        return $stmt->execute();
    }
    /**
     * Delete a donation history record.
     *
     * @param int $id
     * @return bool
     */
    public static function deleteHistoriqueDonsById($id)
    {
        $query = "DELETE FROM historique_dons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
