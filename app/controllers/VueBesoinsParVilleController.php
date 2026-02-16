<?php 

namespace app\controllers;

use flight\Engine;
use app\models\VueBesoinsParVilleModel;

class VueBesoinsparVilleController 
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllBesoinsParVille()
    {
        $view = VueBesoinsParVilleModel::getView();
        return $view;
    }

    public function getBesoinByIdVille($idVille){
    $view = VueBesoinsParVilleModel::getByIdVille($idVille);
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'No Result found'];
    }
    }

    public function getByIdVilleAndTypeDon($idVille,$typeDon){
        $view = VueBesoinsParVilleModel::getByIdVilleAndTypeDon($idVille,$typeDon);
        if($view){
        return $view;
    }else{
        return ['status' => 'error', 'message' => 'No Result found'];
    }
    }

    public function getByIdVilleAndNomDon($idVille,$nomDon){
        $view = VueBesoinsParVilleModel::getByIdVilleAndNomDon($idVille,$nomDon);
     if($view){
        return $view;
    }else{
        return ['status' => 'error', 'message' => 'No Result found'];
    }   
    }

    public function getTotalMontantByVille($idVille){
        $view = VueBesoinsParVilleModel::getTotalMontantByVille($idVille);
        if($view){
        return $view;
    }else{
        return ['status' => 'error', 'message' => 'No Result found'];
    }   
    }
    
    public function getQuantiteParType($idVille){
        $view = VueBesoinsParVilleModel::getQuantiteParType($idVille);
       if($view){
        return $view;
    }else{
        return ['status' => 'error', 'message' => 'No Result found'];
    } 
    }
}