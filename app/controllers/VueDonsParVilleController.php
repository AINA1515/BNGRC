<?php

namespace app\controllers;

use flight\Engine;
use app\models\VueDonsParVilleModel;

class VueDonsParVilleController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllDonsParVille()
    {
        $view = VueDonsParVilleModel::getView();
        if ($view) {
            return $view;
        } else {
            return ['status' => 'error', 'message' => 'Not found'];
        }
    }

    public function getDonParVille($idVille){
        $view = VueDonsParVilleModel::getDonParVille($idVille);
        if ($view) {
            return $view;
        } else {
            return ['status' => 'error', 'message' => 'Not found'];
        }
    }

    public function countDonparType($idVille){
        $view = VueDonsParVilleModel::countDonparType($idVille);
        return $view;
    }
    public function getByIdVilleAndTypeDon($idVille,$typeDon){
        $view = VueDonsParVilleModel::getByIdVilleAndTypeDon($idVille,$typeDon);
        if ($view) {
            return $view;
        } else {
            return ['status' => 'error', 'message' => 'Not found'];
        }
    }

    public function getByIdVilleAndNomDon($idDon,$nomDon){
        $view = VueDonsParVilleModel::getByIdVilleAndNomDon($idDon,$nomDon);
        if ($view) {
            return $view;
        } else {
            return ['status' => 'error', 'message' => 'Not found'];
        }
    }
}