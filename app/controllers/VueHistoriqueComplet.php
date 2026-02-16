<?php 

namespace app\controllers;

use flight\Engine;
use app\models\VueHistoriqueComplet;

class VueHistoriqueCompletController 
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllDonsParVille()
    {
        $view = VueHistoriqueComplet::getView();
        return $view;
    }
  
}