<?php

namespace app\models;

use Flight;

class VueDonsParVilleModel
{
    /**
     *
     * @return array
     */
    public static function getView()
    {
        $query = "SELECT * FROM vue_dons_par_ville";
        return Flight::db()->query($query)->fetchAll();
    }


    public static function getDonParVille($idVille)
    {
        $query = "SELECT typeDon, nomDon, dateDon
              FROM vue_dons_par_ville
              WHERE idVille = :idVille
              ORDER BY dateDon DESC";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function countDonParVille($idVille)
    {
        $query = "SELECT COUNT(*) as total
              FROM vue_dons_par_ville
              WHERE idVille = :idVille";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public static function countDonParType($idVille)
    {
        $query = "SELECT typeDon, COUNT(*) as total
              FROM vue_dons_par_ville
              WHERE idVille = :idVille
              GROUP BY typeDon";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getByIdVilleAndTypeDon($idVille, $typeDon)
    {
        $query = "SELECT typeDon, nomDon, dateDon
              FROM vue_dons_par_ville
              WHERE idVille = :idVille
              AND typeDon = :typeDon
              ORDER BY dateDon DESC";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->bindParam(":typeDon", $typeDon, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getByIdVilleAndNomDon($idVille, $nomDon)
    {
        $query = "SELECT typeDon, nomDon, dateDon
              FROM vue_dons_par_ville
              WHERE idVille = :idVille
              AND nomDon = :nomDon
              ORDER BY dateDon DESC";

        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille, \PDO::PARAM_INT);
        $stmt->bindParam(":nomDon", $nomDon, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
