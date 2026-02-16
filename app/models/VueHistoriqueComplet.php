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
        $query = "SELECT * FROM vue_historique_complet";
        return Flight::db()->query($query)->fetchAll();
    }

}

?>