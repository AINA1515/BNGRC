<?php

namespace app\models;

use Flight;

class VueBesoinsParVilleModel
{
    
    public static function getView()
    {
        $query = "SELECT * FROM vue_besoins_par_ville";
        return Flight::db()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getByIdVille($idVille)
    {
        $query = "SELECT * 
                  FROM vue_besoins_par_ville
                  WHERE idVille = :idVille";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getByIdVilleAndTypeDon($idVille, $typeDon)
    {
        $query = "SELECT * 
                  FROM vue_besoins_par_ville
                  WHERE idVille = :idVille
                  AND typeDon = :typeDon";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->bindParam(":typeDon", $typeDon, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getByIdVilleAndNomDon($idVille, $nomDon)
    {
        $query = "SELECT * 
                  FROM vue_besoins_par_ville
                  WHERE idVille = :idVille
                  AND nomDon = :nomDon";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->bindParam(":nomDon", $nomDon, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getTotalMontantByVille($idVille)
    {
        $query = "SELECT SUM(montantTotal) as totalMontant
                  FROM vue_besoins_par_ville
                  WHERE idVille = :idVille";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getQuantiteParType($idVille)
    {
        $query = "SELECT typeDon, SUM(quantite) as totalQuantite
                  FROM vue_besoins_par_ville
                  WHERE idVille = :idVille
                  GROUP BY typeDon";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
