<?php 

namespace app\controllers;

use flight\Engine;
use app\models\VueHistoriqueCompletModel;

class VueHistoriqueCompletController 
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllHistorique()
    {
        $view = VueHistoriqueCompletModel::getView();
        return $view;
    }

    public function getHistoriquebyTypeDon($typeDon){
    $view = VueHistoriqueCompletModel::getHistoriquebyTypeDon($typeDon);
    if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }

    public function getHistoriqueByNomDon($nomDon){
        $view = VueHistoriqueCompletModel::getHistoriqueByNomDon($nomDon);
        if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }
    
    public function getHistoriqueByNomVille($nomVille){
        $view = VueHistoriqueCompletModel::getHistoriqueByNomVille($nomVille);
        if($view){
    return $view;
    }else{
        return ['status' => 'error', 'message' => 'Not found'];
    }
    }


    
}