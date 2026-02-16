<?php

namespace app\controllers;

use flight\Engine;

class BesoinsVille
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllBesoins()
    {
        $query = "SELECT * FROM besoins";
        return $this->app->db()->query($query)->fetchAll();
    }

    public function getBesoinsByVille($villeId)
    {
        $query = "SELECT * FROM besoins WHERE idVille = :villeId";
        $stmt = $this->app->db()->prepare($query);
        $stmt->bindParam(':villeId', $villeId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBesoinsById($id)
    {
        $query = "SELECT * FROM besoins WHERE id = :id";
        $stmt = $this->app->db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }


    public function addBesoin($idVille, $idDons, $idTypeDons, $quantite, $prixUnitaire)
    {
        $query = "INSERT INTO besoins (idVille, idDons, idTypeDons, quantite, prixUnitaire) VALUES (:idVille, :idDons, :idTypeDons, :quantite, :prixUnitaire)";
        $stmt = $this->app->db()->prepare($query);
        $stmt->bindParam(':idVille', $idVille, \PDO::PARAM_INT);
        $stmt->bindParam(':idDons', $idDons, \PDO::PARAM_INT);
        $stmt->bindParam(':idTypeDons', $idTypeDons, \PDO::PARAM_INT);
        $stmt->bindParam(':quantite', $quantite, \PDO::PARAM_INT);
        $stmt->bindParam(':prixUnitaire', $prixUnitaire, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateBesoin($id, $idVille, $idDons, $idTypeDons, $quantite, $prixUnitaire)
    {
        $query = "UPDATE besoins SET idVille = :idVille, idDons = :idDons, idTypeDons = :idTypeDons, quantite = :quantite, prixUnitaire = :prixUnitaire WHERE id = :id";
        $stmt = $this->app->db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':idVille', $idVille, \PDO::PARAM_INT);
        $stmt->bindParam(':idDons', $idDons, \PDO::PARAM_INT);
        $stmt->bindParam(':idTypeDons', $idTypeDons, \PDO::PARAM_INT);
        $stmt->bindParam(':quantite', $quantite, \PDO::PARAM_INT);
        $stmt->bindParam(':prixUnitaire', $prixUnitaire, \PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function deleteBesoin($id)
    {
        $query = "DELETE FROM besoins WHERE id = :id";
        $stmt = $this->app->db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
