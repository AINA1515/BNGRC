<?php 

namespace app\controllers;

use flight\Engine;
use app\models\VueBesoinsParVilleModel;

class VueBesoinsParVilleController
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
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }
    
}