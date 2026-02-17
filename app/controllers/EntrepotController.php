<?php

namespace app\controllers;

use Flight;
use app\models\EntrepotModel;
use app\models\ModeleDonsModel;

class EntrepotController
{
    public static function index()
    {
    $stocks = EntrepotModel::getAllStock();
    $modeles = ModeleDonsModel::getAllModeles();
    Flight::view()->render('entrepot', ['stocks' => $stocks, 'modeles' => $modeles]);
    }

    public static function addStock()
    {
        $req = Flight::request();
        $idModele = $req->data->idModele ?? null;
        $quantite = $req->data->quantite ?? 0;
        if (!$idModele || (int)$quantite <= 0) {
            Flight::halt(400, 'Invalid parameters');
        }
        $ok = EntrepotModel::addStock($idModele, $quantite);
        if ($ok) {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/entrepot'));
            exit;
        }
        Flight::halt(500, 'Failed to add stock');
    }

    public static function setStock()
    {
        $req = Flight::request();
        $idModele = $req->data->idModele ?? null;
        $quantite = $req->data->quantite ?? 0;
        if (!$idModele) Flight::halt(400, 'missing idModele');
        EntrepotModel::setStock($idModele, $quantite);
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/entrepot'));
        exit;
    }
}
