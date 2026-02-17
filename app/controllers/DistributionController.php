<?php

namespace app\controllers;

use flight\Engine;
use app\models\DistributionModel;
use Flight;

class DistributionController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function index()
    {
        // Show distribution UI: list of besoins that can be purchased (type argent) and recent distributions
        $besoins = \app\models\BesoinVilleModel::getAllBesoins();
        $distributions = DistributionModel::getRecentDistributions();
        $types = \app\models\TypeDonsModel::getAllTypes();
        $modeles = \app\models\ModeleDonsModel::getAllModeles();

        // Compute available money donations (total donations of type 'Argent' minus purchases in achat)
        $typeArgent = \app\models\TypeDonsModel::getTypeByName('Argent');
        $typeArgentId = $typeArgent ? (int)$typeArgent['id'] : 0;
        $totalMoney = 0.0;
        if ($typeArgentId > 0) {
            $donsMoney = \app\models\DonsModel::getDonationsByIdType($typeArgentId);
            foreach ($donsMoney as $d) {
                $totalMoney += ((float)($d['quantite'] ?? 0)) * ((float)($d['prixUnitaire'] ?? 0));
            }
        }
    $totalSpent = (float)\Flight::db()->query('SELECT COALESCE(SUM(quantite * prixUnitaire * (1 + pourcentageAchat/100)),0) FROM achat')->fetchColumn();
        $availableMoney = max(0, $totalMoney - $totalSpent);

        $this->app->render('distribution', [
            'csp_nonce' => $this->app->get('csp_nonce'),
            'besoins' => $besoins,
            'distributions' => $distributions,
            'types' => $types,
            'modeles' => $modeles,
            'availableMoney' => $availableMoney,
        ]);
    }

    /**
     * Buy items for a besoin using money donations (type 'Argent')
     * Expects POST: idBesoin, quantiteToBuy, pourcentageAchat (optional)
     */
    public function purchase()
    {
        $req = $this->app->request();
        $distributionId = (int)($req->data->distributionId ?? 0);
        $quantiteToBuy = (int)($req->data->quantiteToBuy ?? 0);
        $pourcentage = isset($req->data->pourcentageAchat) ? (float)$req->data->pourcentageAchat : null;

        if ($distributionId <= 0 || $quantiteToBuy <= 0) {
            $this->app->halt(400, 'Invalid parameters');
        }

        $distribution = DistributionModel::getById($distributionId);
        if (!$distribution) {
            $this->app->halt(404, 'Distribution row not found');
        }

        $idBesoin = (int)$distribution['idBesoins'];
        $besoin = \app\models\BesoinVilleModel::getBesoinsById($idBesoin);
        if (!$besoin) {
            $this->app->halt(404, 'Besoin not found');
        }

        // Determine available money donations (type 'Argent') grouped
        $typeArgent = \app\models\TypeDonsModel::getTypeByName('Argent');
        $typeArgentId = $typeArgent ? (int)$typeArgent['id'] : 0;

        // Compute total money available
        $dons = \app\models\DonsModel::getDonationsByIdType($typeArgentId);
        $totalMoney = 0.0;
        foreach ($dons as $d) {
            $totalMoney += ((float)($d['quantite'] ?? 0)) * ((float)($d['prixUnitaire'] ?? 0));
        }

        // Cost to buy quantiteToBuy at besoin prixUnitaire
        $unitPrice = (float)($besoin['prixUnitaire'] ?? 0);
        $cost = $unitPrice * $quantiteToBuy;

        $appliedPourcentage = 0.0;
        if ($pourcentage !== null) {
            $appliedPourcentage = (float)$pourcentage;
        } else {
            // default percentage from achat table if exists
            $row = \Flight::db()->query('SELECT pourcentageAchat FROM achat ORDER BY id DESC LIMIT 1')->fetchColumn();
            if ($row !== false) $appliedPourcentage = (float)$row;
        }
        $cost *= (1 + $appliedPourcentage / 100.0);

        if ($totalMoney < $cost) {
            // Not enough money
            $this->app->redirect('/distribution?purchase=insufficient');
            return;
        }

        // Deduct money from donations (oldest-first) and update dons.quantite accordingly
        $remainingCost = $cost;
        // Sort dons by date_ asc (oldest first)
        usort($dons, function ($a, $b) {
            $ta = isset($a['date_']) ? strtotime($a['date_']) : 0;
            $tb = isset($b['date_']) ? strtotime($b['date_']) : 0;
            return $ta <=> $tb;
        });
        foreach ($dons as $d) {
            if ($remainingCost <= 0) break;
            $donId = (int)$d['id'];
            $donQty = (int)($d['quantite'] ?? 0);
            $donPrice = (float)($d['prixUnitaire'] ?? 0);
            if ($donQty <= 0 || $donPrice <= 0) continue;
            $donTotalValue = $donQty * $donPrice;
            if ($donTotalValue <= 0) continue;
            if ($donTotalValue >= $remainingCost) {
                // We consume partial quantity from this donation
                $qtyToConsume = floor($remainingCost / $donPrice);
                if ($qtyToConsume <= 0) $qtyToConsume = 1; // at least 1 if money insufficiently granular
                $newQty = max(0, $donQty - $qtyToConsume);
                \app\models\DonsModel::updateDonation($donId, $d['idModeleDons'] ?? 0, $d['idTypeDons'] ?? 0, $newQty, $d['prixUnitaire'] ?? null, $d['date_'] ?? null);
                $remainingCost -= $qtyToConsume * $donPrice;
                break;
            } else {
                // consume whole donation
                \app\models\DonsModel::updateDonation($donId, $d['idModeleDons'] ?? 0, $d['idTypeDons'] ?? 0, 0, $d['prixUnitaire'] ?? null, $d['date_'] ?? null);
                $remainingCost -= $donTotalValue;
            }
        }

        // Update besoin quantity (subtract bought quantity)
        $newBesoinQty = max(0, (int)$besoin['quantite'] - $quantiteToBuy);
        \app\models\BesoinVilleModel::updateBesoin((int)$besoin['id'], (int)$besoin['idVille'], (int)$besoin['idModeleDons'], $newBesoinQty, $besoin['prixUnitaire'] ?? null);

        // Update distribution row to mark distributed quantity and remaining besoin
        DistributionModel::updateDistribution($distributionId, [
            'quantiteBesoinRestant' => $newBesoinQty,
            'quantiteDonsDistribue' => ((int)$distribution['quantiteDonsDistribue'] + $quantiteToBuy),
            'date_' => date('Y-m-d H:i:s')
        ]);

        // Insert achat record
        $q = \Flight::db()->prepare('INSERT INTO achat (idDons, date_, quantite, pourcentageAchat, prixUnitaire) VALUES (:idDons, :date_, :quantite, :pourcentage, :prix)');
        $q->execute([
            ':idDons' => isset($dons[0]['id']) ? (int)$dons[0]['id'] : null,
            ':date_' => date('Y-m-d H:i:s'),
            ':quantite' => $quantiteToBuy,
            ':pourcentage' => $appliedPourcentage,
            ':prix' => $unitPrice,
        ]);

        $this->app->redirect('/distribution?purchase=ok');
    }
}
