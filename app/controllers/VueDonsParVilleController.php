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
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }

    public function getDonsParVile($idVille){
        $view = VueDonsParVilleModel::getdonParVille($idVille);
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }

    public function getcountDonsParVile($idVille){
        $view = VueDonsParVilleModel::countDonParVille($idVille);
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }

    public function countDonParType($idVille){
        $view = VueDonsParVilleModel::countDonParType($idVille);
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }

    public function getByIdVilleAndTypeDon($idVille, $typeDon){
        $view = VueDonsParVilleModel::getByIdVilleAndTypeDon($idVille, $typeDon);
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }}

    public function getByIdVilleAndNomDon($idVille, $nomDon){
        $view = VueDonsParVilleModel::getByIdVilleAndNomDon($idVille, $nomDon);
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }}

}