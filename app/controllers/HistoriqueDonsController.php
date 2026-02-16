<?php

namespace app\controllers;

use flight\Engine;
use app\models\HistoriqueDonsModel;

class HistoriqueDonsController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllHistoriqueDons()
    {
        return HistoriqueDonsModel::getAllHistoriqueDons();
    }

    public function getHistoriqueDonsById($id)
    {
        $historique = HistoriqueDonsModel::getHistoriqueDonsById($id);
        if ($historique) {
            return $historique;
        } else {
            return ['status' => 'error', 'message' => 'Historique not found'];
        }
    }

    public function addHistoriqueDons($idDons, $date_, $idVille)
    {
        if (HistoriqueDonsModel::addHistoriqueDons($idDons, $date_, $idVille)) {
            return ['status' => 'success', 'message' => 'Historique added successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add historique'];
        }
    }
    public function updateHistoriqueDons($id, $idDons, $date_, $idVille)
    {
        if (HistoriqueDonsModel::updateHistoriqueDons($id, $idDons, $date_, $idVille)) {
            return ['status' => 'success', 'message' => 'Historique updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update historique'];
        }
    }
    public function deleteHistoriqueDons($id)
    {
        if (HistoriqueDonsModel::deleteHistoriqueDonsById($id)) {
            return ['status' => 'success', 'message' => 'Historique deleted successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete historique'];
        }
    }
}
