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

    public function getAllHistorique()
    {
        $view = VueHistoriqueComplet::getView();
        return $view;
    }

    public function getHistoriquebyTypeDon($typeDon){
    $view = VueHistoriqueComplet::getHistoriquebyTypeDon($typeDon);
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }

    public function getHistoriqueByNomDon($nomDon){
        $view = VueHistoriqueComplet::getHistoriqueByNomDon($nomDon);
        if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }
    
    public function getHistoriqueByNomVille($nomVille){
        $view = VueHistoriqueComplet::getHistoriqueByNomVille($nomVille);
        if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }


    
}