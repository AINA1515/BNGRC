<?php

namespace app\models;

use Flight;
use flight\Engine;

class BesoinVilleModel
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public static function getAllBesoins()
    {
        $query = "SELECT * FROM besoins";
        return  Flight::db()->query($query)->fetchAll();
    }

    public static function getBesoinsByVille($villeId)
    {
        $query = "SELECT * FROM besoins WHERE idVille = :villeId";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':villeId', $villeId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getBesoinsById($id)
    {
        $query = "SELECT * FROM besoins WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }


    public static function addBesoin($idVille, $idDons, $idTypeDons, $quantite, $prixUnitaire)
    {
        $query = "INSERT INTO besoins (idVille, idDons, idTypeDons, quantite, prixUnitaire) VALUES (:idVille, :idDons, :idTypeDons, :quantite, :prixUnitaire)";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':idVille', $idVille, \PDO::PARAM_INT);
        $stmt->bindParam(':idDons', $idDons, \PDO::PARAM_INT);
        $stmt->bindParam(':idTypeDons', $idTypeDons, \PDO::PARAM_INT);
        $stmt->bindParam(':quantite', $quantite, \PDO::PARAM_INT);
        $stmt->bindParam(':prixUnitaire', $prixUnitaire, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    public static function updateBesoin($id, $idVille, $idDons, $idTypeDons, $quantite, $prixUnitaire)
    {
        $query = "UPDATE besoins SET idVille = :idVille, idDons = :idDons, idTypeDons = :idTypeDons, quantite = :quantite, prixUnitaire = :prixUnitaire WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':idVille', $idVille, \PDO::PARAM_INT);
        $stmt->bindParam(':idDons', $idDons, \PDO::PARAM_INT);
        $stmt->bindParam(':idTypeDons', $idTypeDons, \PDO::PARAM_INT);
        $stmt->bindParam(':quantite', $quantite, \PDO::PARAM_INT);
        $stmt->bindParam(':prixUnitaire', $prixUnitaire, \PDO::PARAM_STR);
        return $stmt->execute();
    }
    public static function deleteBesoin($id)
    {
        $query = "DELETE FROM besoins WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}