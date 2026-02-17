<?php 

namespace app\controllers;

use flight\Engine;
use app\models\BesoinVilleModel;

class BesoinVilleController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllBesoins()
    {
        $besoins = BesoinVilleModel::getAllBesoins();
        return $besoins;
    }

    public function getBesoinsByVille($villeId)
    {
        $besoins = BesoinVilleModel::getBesoinsByVille($villeId);
        return $besoins;
    }

    public function getBesoinById($id)
    {
        $besoin = BesoinVilleModel::getBesoinsById($id);
        if ($besoin) {
            return $besoin;
        } else {
            return ['status' => 'error', 'message' => 'Besoin not found'];
        }
    }

    public function addBesoin($idVille, $idModeleDons, $quantite, $prixUnitaire)
    {
        if (BesoinVilleModel::addBesoin($idVille, $idModeleDons, $quantite, $prixUnitaire)) {
            return ['status' => 'success', 'message' => 'Besoin added successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add besoin']; 
        }
    }

    public function updateBesoin($id, $idVille, $idModeleDons, $quantite, $prixUnitaire)
    {
        if (BesoinVilleModel::updateBesoin($id, $idVille, $idModeleDons, $quantite, $prixUnitaire)) {
            return ['status' => 'success', 'message' => 'Besoin updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update besoin'];
        }
    }

    public function deleteBesoin($id)
    {
        if (BesoinVilleModel::deleteBesoin($id)) {
            return ['status' => 'success', 'message' => 'Besoin deleted successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete besoin'];
        }
    }
}