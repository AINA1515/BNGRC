<?php 

namespace app\controllers;

use flight\Engine;
use app\models\VueDonsParVille;

class VueDonsParVilleController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllDonsParVille()
    {
        $view = VueDonsParVille::getView();
        return $view;
    }

    public function getDonsParVile($idVille){
        $view = VueDonsParVille::getdonParVille($idVille);
        return $view;
    }
  
}