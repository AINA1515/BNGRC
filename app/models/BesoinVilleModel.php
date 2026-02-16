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
        $query = "SELECT * FROM besoinsVille";
        return  Flight::db()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getBesoinsByVille($villeId)
    {
        $query = "SELECT * FROM besoinsVille WHERE idVille = :villeId";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':villeId', $villeId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getBesoinsById($id)
    {
        $query = "SELECT * FROM besoinsVille WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public static function addBesoin($idVille, $idDons, $quantite, $prixUnitaire)
    {
        // idTypeDons removed from besoinsVille; it's derived from dons when needed
        $query = "INSERT INTO besoinsVille (idVille, idDons, quantite, prixUnitaire) VALUES (:idVille, :idDons, :quantite, :prixUnitaire)";
        $stmt = Flight::db()->prepare($query);
        // bind values with proper types; prixUnitaire may be null or numeric
        $stmt->bindValue(':idVille', (int)$idVille, \PDO::PARAM_INT);
        $stmt->bindValue(':idDons', (int)$idDons, \PDO::PARAM_INT);
        $stmt->bindValue(':quantite', (int)$quantite, \PDO::PARAM_INT);
        if ($prixUnitaire === null || $prixUnitaire === '') {
            $stmt->bindValue(':prixUnitaire', null, \PDO::PARAM_NULL);
        } else {
            // store as string/decimal format
            $stmt->bindValue(':prixUnitaire', (string)$prixUnitaire, \PDO::PARAM_STR);
        }
        $ok = $stmt->execute();
        if (!$ok) {
            // log diagnostic info for debugging
            $err = $stmt->errorInfo();
            $payload = [
                'time' => date('c'),
                'idVille' => $idVille,
                'idDons' => $idDons,
                'quantite' => $quantite,
                'prixUnitaire' => $prixUnitaire,
                'error' => $err
            ];
            @file_put_contents('/tmp/besoin_insert.log', json_encode($payload) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        return $ok;
    }

    public static function updateBesoin($id, $idVille, $idDons, $quantite, $prixUnitaire)
    {
        $query = "UPDATE besoinsVille SET idVille = :idVille, idDons = :idDons, quantite = :quantite, prixUnitaire = :prixUnitaire WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindValue(':id', (int)$id, \PDO::PARAM_INT);
        $stmt->bindValue(':idVille', (int)$idVille, \PDO::PARAM_INT);
        $stmt->bindValue(':idDons', (int)$idDons, \PDO::PARAM_INT);
        $stmt->bindValue(':quantite', (int)$quantite, \PDO::PARAM_INT);
        if ($prixUnitaire === null || $prixUnitaire === '') {
            $stmt->bindValue(':prixUnitaire', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':prixUnitaire', (string)$prixUnitaire, \PDO::PARAM_STR);
        }
        return $stmt->execute();
    }
    public static function deleteBesoin($id)
    {
        $query = "DELETE FROM besoinsVille WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
