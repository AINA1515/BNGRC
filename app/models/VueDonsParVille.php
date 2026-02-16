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

}

?>