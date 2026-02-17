<?php 

namespace app\controllers;

use flight\Engine;
use app\models\BesoinVilleModel;
use Flight;

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

    // Appliquer la simulation : soustraire les quantités utilisées des dons (FIFO), supprimer les dons épuisés
    public function applySimulation()
    {
        // Debug log start
        @file_put_contents('/tmp/apply_simulation.log', date('c') . " -- START --\n", FILE_APPEND | LOCK_EX);
        // 1. Récupérer tous les dons du plus ancien au plus récent
        $dons = \app\models\DonsModel::getAllDonations();
        usort($dons, function($a, $b) {
            $dateA = isset($a['date_']) ? strtotime($a['date_']) : 0;
            $dateB = isset($b['date_']) ? strtotime($b['date_']) : 0;
            if ($dateA === $dateB) return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
            return $dateA <=> $dateB;
        });

        // 2. Recalculer la simulation pour obtenir les allocations (mode par défaut : proportionnel)
        $besoins = \app\models\BesoinVilleModel::getAllBesoins();
        $allocResult = \app\models\BesoinVilleModel::simulateAllocation('proportionnel');
        $allocs = [];
        if (is_array($allocResult) && isset($allocResult['result']) && is_array($allocResult['result'])) {
            foreach ($allocResult['result'] as $row) {
                if (isset($row['id']) && isset($row['sim_donnee'])) {
                    $allocs[$row['id']] = $row['sim_donnee'];
                }
            }
        }
        @file_put_contents('/tmp/apply_simulation.log', date('c') . " -- ALLOCS -- " . json_encode($allocs) . "\n", FILE_APPEND | LOCK_EX);

        // 3. Mettre à jour les besoins avec les valeurs obtenues depuis la simulation
        foreach ($besoins as $besoin) {
            $bid = $besoin['id'];
            $qteInit = (int)$besoin['quantite'];
            $qteAffectee = $allocs[$bid] ?? 0;
            $qteRestante = max(0, $qteInit - $qteAffectee);
            if ($qteRestante > 0) {
                $ok = \app\models\BesoinVilleModel::updateBesoin($bid, $besoin['idVille'], $besoin['idModeleDons'], $qteRestante, $besoin['prixUnitaire']);
                @file_put_contents('/tmp/apply_simulation.log', date('c') . " UPDATE besoin $bid to $qteRestante => " . ($ok ? 'OK' : 'FAIL') . "\n", FILE_APPEND | LOCK_EX);
            } else {
                $ok = \app\models\BesoinVilleModel::deleteBesoin($bid);
                @file_put_contents('/tmp/apply_simulation.log', date('c') . " DELETE besoin $bid => " . ($ok ? 'OK' : 'FAIL') . "\n", FILE_APPEND | LOCK_EX);
            }
        }

        // 4. Pour chaque don, soustraire la quantité utilisée (par modèle) et supprimer ceux à 0
        // On regroupe les allocations par modèle
        $allocsByModele = [];
        foreach ($besoins as $besoin) {
            $bid = $besoin['id'];
            $idModele = $besoin['idModeleDons'];
            $qteAffectee = $allocs[$bid] ?? 0;
            if (!isset($allocsByModele[$idModele])) $allocsByModele[$idModele] = 0;
            $allocsByModele[$idModele] += $qteAffectee;
        }

        // Pour chaque modèle, traiter les dons FIFO
        foreach ($allocsByModele as $idModele => $qteAUtiliser) {
            if ($qteAUtiliser <= 0) continue;
            // Récupérer les dons de ce modèle, du plus ancien au plus récent
            $donsModele = array_filter($dons, function($d) use ($idModele) {
                return $d['idModeleDons'] == $idModele;
            });
            usort($donsModele, function($a, $b) {
                $dateA = isset($a['date_']) ? strtotime($a['date_']) : 0;
                $dateB = isset($b['date_']) ? strtotime($b['date_']) : 0;
                if ($dateA === $dateB) return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
                return $dateA <=> $dateB;
            });
            foreach ($donsModele as $don) {
                if ($qteAUtiliser <= 0) break;
                $did = $don['id'];
                $qteDon = (int)$don['quantite'];
                if ($qteDon <= 0) continue;
                $qteAttribuee = min($qteAUtiliser, $qteDon);
                // Trouver toutes les villes qui ont reçu ce modèle et la quantité à attribuer
                foreach ($besoins as $besoin) {
                    if ($besoin['idModeleDons'] == $idModele && $qteAttribuee > 0) {
                        $bid = $besoin['id'];
                        $qteAffectee = $allocs[$bid] ?? 0;
                        if ($qteAffectee > 0) {
                            $qtePourVille = min($qteAffectee, $qteAttribuee);
                            \app\models\HistoriqueDonsModel::addHistorique($besoin['idVille'], $did, $qtePourVille);
                            $qteAttribuee -= $qtePourVille;
                            $allocs[$bid] -= $qtePourVille;
                        }
                    }
                }
                if ($qteAUtiliser >= $qteDon) {
                    // On consomme tout ce don
                    $ok = \app\models\DonsModel::deleteDonation($did);
                    @file_put_contents('/tmp/apply_simulation.log', date('c') . " DELETE don $did => " . ($ok ? 'OK' : 'FAIL') . "\n", FILE_APPEND | LOCK_EX);
                    $qteAUtiliser -= $qteDon;
                } else {
                    // On consomme partiellement ce don
                    $newQte = $qteDon - $qteAUtiliser;
                    $donType = $don['idTypeDons'] ?? null;
                    $donPrix = $don['prixUnitaire'] ?? null;
                    $donDate = $don['date_'] ?? null;
                    $ok = \app\models\DonsModel::updateDonation($did, $idModele, $donType, $newQte, $donPrix, $donDate);
                    @file_put_contents('/tmp/apply_simulation.log', date('c') . " UPDATE don $did to $newQte => " . ($ok ? 'OK' : 'FAIL') . "\n", FILE_APPEND | LOCK_EX);
                    $qteAUtiliser = 0;
                }
            }
        }
        @file_put_contents('/tmp/apply_simulation.log', date('c') . " -- END --\n", FILE_APPEND | LOCK_EX);

        Flight::redirect('/');
    }
    
}