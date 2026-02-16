<?php

namespace app\models;

use Flight;

class VueHistoriqueComplet
{
    /**
     *
     * @return array
     */
    public static function getView()
    {
        $query = "SELECT * FROM vue_historique_complet ORDER BY dateDon DESC";
        return Flight::db()->query($query)->fetchAll();
    }

    public static function getHistoriqueByTypeDon($typeDon){
        $query = "select * from vue_historique_complet where typeDon = :typeDon";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":typeDon", $typeDon,\PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getHistoriqueByNomDon($nomDon){
        $query = "select * from vue_historique_complet where nomDon = :nomDon";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":nomDon", $nomDon,\PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getHistoriqueByNomVille($nomVille){
        $query = "select * from vue_historique_complet where nomVille = :nom";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":nom", $nomVille,\PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    

}

?>