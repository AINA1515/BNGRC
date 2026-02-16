<?php

namespace app\models;

use Flight;

class VueDonsParVille
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

    public static function getdonParVille($idVille){
        $query = "select typeDon,nomDon,dateDon from vue_dons_par_ville WHERE idVille = :id ";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(":idVille", $idVille,\PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }



}

?>