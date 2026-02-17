<?php

namespace app\models;

use Flight;
use flight\Engine;

class BesoinVilleModel
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public static function getAllBesoins()
    {
        $query = "SELECT * FROM besoinsVille";
        return  Flight::db()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getBesoinsByVille($villeId)
    {
        $query = "SELECT * FROM besoinsVille WHERE idVille = :villeId";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':villeId', $villeId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getBesoinsById($id)
    {
        $query = "SELECT * FROM besoinsVille WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public static function addBesoin($idVille, $idModeleDons, $quantite, $prixUnitaire, $date = null)
    {
        $query = "INSERT INTO besoinsVille (idVille, idModeleDons, quantite, prixUnitaire, date_) VALUES (:idVille, :idModeleDons, :quantite, :prixUnitaire, :date_)";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindValue(':idVille', (int)$idVille, \PDO::PARAM_INT);
        $stmt->bindValue(':idModeleDons', (int)$idModeleDons, \PDO::PARAM_INT);
        $stmt->bindValue(':quantite', (int)$quantite, \PDO::PARAM_INT);
        if ($prixUnitaire === null || $prixUnitaire === '') {
            $stmt->bindValue(':prixUnitaire', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':prixUnitaire', (string)$prixUnitaire, \PDO::PARAM_STR);
        }
        if ($date === null || trim($date) === '') {
            $stmt->bindValue(':date_', date('Y-m-d H:i:s'));
        } else {
            $ts = strtotime($date);
            $stmt->bindValue(':date_', $ts === false ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $ts));
        }
        $ok = $stmt->execute();
        if (!$ok) {
            $err = $stmt->errorInfo();
            $payload = [
                'time' => date('c'),
                'idVille' => $idVille,
                'idModeleDons' => $idModeleDons,
                'quantite' => $quantite,
                'prixUnitaire' => $prixUnitaire,
                'error' => $err
            ];
            @file_put_contents('/tmp/besoin_insert.log', json_encode($payload) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        return $ok;
    }

    public static function updateBesoin($id, $idVille, $idModeleDons, $quantite, $prixUnitaire)
    {
        $query = "UPDATE besoinsVille SET idVille = :idVille, idModeleDons = :idModeleDons, quantite = :quantite, prixUnitaire = :prixUnitaire WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindValue(':id', (int)$id, \PDO::PARAM_INT);
        $stmt->bindValue(':idVille', (int)$idVille, \PDO::PARAM_INT);
        $stmt->bindValue(':idModeleDons', (int)$idModeleDons, \PDO::PARAM_INT);
        $stmt->bindValue(':quantite', (int)$quantite, \PDO::PARAM_INT);
        if ($prixUnitaire === null || $prixUnitaire === '') {
            $stmt->bindValue(':prixUnitaire', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':prixUnitaire', (string)$prixUnitaire, \PDO::PARAM_STR);
        }
        return $stmt->execute();
    }
    public static function deleteBesoin($id)
    {
        $query = "DELETE FROM besoinsVille WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Returns an associative map of "{idVille}_{idDons}" => totalDonnee
     * based on the historiqueDons sums. Used to compute donated quantities per ville/don.
     *
     * @return array
     */
    public static function getDonSumsMap()
    {
        $query = "SELECT h.idVille as idVille, h.idDons as idDons, SUM(d.quantite) as totalDonnee FROM historiqueDons h JOIN dons d on d.id = h.idDons GROUP BY h.idVille, h.idDons";
        $rows = Flight::db()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $map = [];
        foreach ($rows as $s) {
            $idVilleSum = $s['idVille'] ?? null;
            $idDonsSum = $s['idDons'] ?? null;
            if ($idVilleSum !== null && $idDonsSum !== null) {
                $key = $idVilleSum . '_' . $idDonsSum;
                $map[$key] = (int) ($s['totalDonnee'] ?? 0);
            }
        }
        return $map;
    }

    /**
     * Build the enriched besoins array suitable for the dashboard view.
     * Combines villes, dons, besoins and historique sums into a single structure.
     *
     * @return array
     */
    public static function getEnrichedBesoinsForDashboard()
    {
        $besoinsRaw = self::getAllBesoins();
        $villeMap = [];
        $villes = VilleModel::getAllCities();
        if (is_array($villes)) {
            foreach ($villes as $v) {
                if (isset($v['id'])) {
                    $villeMap[$v['id']] = $v['nom'] ?? '';
                }
            }
        }
        // build modele dons map
        $modeles = \app\models\ModeleDonsModel::getAllModeles();
        $modeleMap = [];
        if (is_array($modeles)) {
            foreach ($modeles as $m) {
                $modeleMap[$m['id']] = $m['nom'];
            }
        }
        // was actually donated to a ville for a given don id
        $histRows = Flight::db()->query('SELECT h.idVille as idVille, h.idDons as idDons, SUM(d.quantite) as totalDonnee FROM historiqueDons h JOIN dons d on d.id = h.idDons GROUP BY h.idVille, h.idDons')->fetchAll(\PDO::FETCH_ASSOC);
        $donMap = []; // key: "{idVille}_{groupKey}" => totalDonnee
        if (is_array($histRows)) {
            foreach ($histRows as $h) {
                $hVille = isset($h['idVille']) ? $h['idVille'] : null;
                $hDonId = isset($h['idDons']) ? (int)$h['idDons'] : null;
                $hTotal = isset($h['totalDonnee']) ? (int)$h['totalDonnee'] : 0;
                if ($hVille === null || $hDonId === null) continue;
                $groupKey = $donIdToGroup[$hDonId] ?? null;
                if ($groupKey === null) continue;
                $mapKey = $hVille . '_' . $groupKey;
                if (!isset($donMap[$mapKey])) $donMap[$mapKey] = 0;
                $donMap[$mapKey] += $hTotal;
            }
        }

        $besoinVilles = [];
        if (is_array($besoinsRaw)) {
            foreach ($besoinsRaw as $b) {
                $idVille = $b['idVille'] ?? null;
                $idModele = isset($b['idModeleDons']) ? (int)$b['idModeleDons'] : null;
                $initial = isset($b['quantite']) ? (int)$b['quantite'] : 0;
                $besoinVilles[] = [
                    'id' => $b['id'] ?? null,
                    'idVille' => $idVille,
                    'nomVille' => $villeMap[$idVille] ?? ($b['nomVille'] ?? ''),
                    'idModeleDons' => $idModele,
                    'nomDon' => $modeleMap[$idModele] ?? ($b['nomDon'] ?? ''),
                    'quantite' => $initial,
                    'donnee' => null, // à calculer si besoin d'afficher la quantité donnée
                    'restant' => null, // à calculer si besoin d'afficher le restant
                    'prixUnitaire' => $b['prixUnitaire'] ?? null,
                    'date_' => $b['date_'] ?? null
                ];
            }
        }
        return $besoinVilles;
    }

    /**
     * Build a small besoin list suitable for the Dons form (ville name, don name, quantite, pu)
     *
     * @return array
     */
    public static function getBesoinsForForm()
    {
        $besoinsRaw = self::getAllBesoins();
        $villes = VilleModel::getAllCities();
        $donsAll = DonsModel::getAllDonations();

        $villeMap = [];
        if (is_array($villes)) {
            foreach ($villes as $v) {
                $villeMap[$v['id']] = $v['nom'] ?? '';
            }
        }
        $donsMap = [];
        if (is_array($donsAll)) {
            foreach ($donsAll as $d) {
                $donsMap[$d['id']] = $d['nom'] ?? '';
            }
        }

        $list = [];
        if (is_array($besoinsRaw)) {
            foreach ($besoinsRaw as $b) {
                $list[] = [
                    'ville' => $villeMap[$b['idVille'] ?? null] ?? ($b['nomVille'] ?? ''),
                    'besoin' => $donsMap[$b['idDons'] ?? null] ?? ($b['nomDon'] ?? ''),
                    'quantite' => $b['quantite'] ?? 0,
                    'pu' => $b['prixUnitaire'] ?? null
                ];
            }
        }
        return $list;
    }

    /**
     * Simulate allocation of available donations to besoins.
     * Returns ['result' => [...], 'available' => [...]] where result contains per-besoin simulated donnee/restant
     */
    public static function simulateAllocation($mode = 'priorite')
    {
        // load besoins and aggregated dons (grouped by name+type)
        $besoins = self::getAllBesoins();
        $aggregated = DonsModel::getAggregatedDonations();

        // build group totals and map of don id -> groupKey
        $groupTotals = []; // groupKey => totalQuantite
        $donIdToGroup = []; // donId => groupKey
        $allDons = DonsModel::getAllDonations();
        if (is_array($aggregated)) {
            foreach ($aggregated as $g) {
                $gname = isset($g['nom']) ? trim(mb_strtolower($g['nom'])) : '';
                $gidType = isset($g['idTypeDons']) ? (int)$g['idTypeDons'] : 0;
                $gkey = $gname . '::' . $gidType;
                $groupTotals[$gkey] = (int)($g['quantite'] ?? 0);
            }
        }
        if (is_array($allDons)) {
            foreach ($allDons as $d) {
                $did = isset($d['id']) ? (int)$d['id'] : 0;
                $dname = isset($d['nom']) ? trim(mb_strtolower($d['nom'])) : '';
                $dtype = isset($d['idTypeDons']) ? (int)$d['idTypeDons'] : 0;
                $donIdToGroup[$did] = $dname . '::' . $dtype;
            }
        }

        // For simulation we want to allocate from the full group totals (ignore historique)
        // so available per group is simply the groupTotals
        $availableGroup = $groupTotals;

        // prepare result entries
        $resultMap = [];
        if (is_array($besoins)) {
            foreach ($besoins as $b) {
                $bid = (int)($b['id'] ?? 0);
                $resultMap[$bid] = [
                    'id' => $bid,
                    'idVille' => $b['idVille'] ?? null,
                    'idDons' => $b['idDons'] ?? null,
                    'initial' => (int)($b['quantite'] ?? 0),
                    'sim_donnee' => 0,
                    'sim_restant' => 0
                ];
            }
        }

        // Regroupement par modèle (stock partagé pour tous les besoins du même modèle)
        $besoinsByModele = [];
        if (is_array($besoins)) {
            foreach ($besoins as $b) {
                $bid = (int)($b['id'] ?? 0);
                $idModele = isset($b['idModeleDons']) ? $b['idModeleDons'] : null;
                $dateRaw = $b['date_'] ?? null;
                $ts = false;
                if (!empty($dateRaw)) $ts = strtotime($dateRaw);
                if ($ts === false || $ts === null) $ts = PHP_INT_MAX;
                if (!isset($besoinsByModele[$idModele])) $besoinsByModele[$idModele] = [];
                $besoinsByModele[$idModele][] = [
                    'id' => $bid,
                    'initial' => $resultMap[$bid]['initial'] ?? 0,
                    'date_ts' => $ts,
                ];
            }
        }

        // Calcul du stock initial par modèle (somme des dons)
        $stockParModele = [];
        foreach ($allDons as $don) {
            $idModele = isset($don['idModeleDons']) ? $don['idModeleDons'] : null;
            $stockParModele[$idModele] = ($stockParModele[$idModele] ?? 0) + (int)($don['quantite'] ?? 0);
        }

        // Pour chaque modèle, distribuer le stock aux besoins selon le mode
        foreach ($besoinsByModele as $idModele => $bitems) {
            $avail = $stockParModele[$idModele] ?? 0;
            if ($mode === 'priorite') {
                // plus ancien prioritaire
                usort($bitems, function ($a, $b) {
                    if ($a['date_ts'] === $b['date_ts']) return $a['id'] <=> $b['id'];
                    return $a['date_ts'] <=> $b['date_ts'];
                });
                foreach ($bitems as $item) {
                    $bid = $item['id'];
                    $needInitial = $item['initial'];
                    $alloc = min($needInitial, $avail);
                    $resultMap[$bid]['sim_donnee'] = $alloc;
                    $avail -= $alloc;
                    $resultMap[$bid]['sim_restant'] = $avail;
                }
            } elseif ($mode === 'min') {
                // plus petit besoin prioritaire
                usort($bitems, function ($a, $b) {
                    if ($a['initial'] === $b['initial']) return $a['id'] <=> $b['id'];
                    return $a['initial'] <=> $b['initial'];
                });
                foreach ($bitems as $item) {
                    $bid = $item['id'];
                    $needInitial = $item['initial'];
                    $alloc = min($needInitial, $avail);
                    $resultMap[$bid]['sim_donnee'] = $alloc;
                    $avail -= $alloc;
                    $resultMap[$bid]['sim_restant'] = $avail;
                }
            } elseif ($mode === 'proportionnel') {
                // Nouvelle formule : pour chaque besoin, on calcule floor(stock / besoin) et on alloue min(besoin, stock)
                // Calculer toutes les allocations avec le stock initial (ne pas décrémenter le stock entre les besoins)
                $allocs = [];
                $totalAllocated = 0;
                foreach ($bitems as $item) {
                    $bid = $item['id'];
                    $needInitial = $item['initial'];
                    if ($needInitial > 0 && $avail > 0) {
                        $alloc = min($needInitial, floor($avail / $needInitial));
                    } else {
                        $alloc = 0;
                    }
                    $allocs[$bid] = $alloc;
                    $totalAllocated += $alloc;
                }
                $stockFinal = $avail - $totalAllocated;
                foreach ($bitems as $item) {
                    $bid = $item['id'];
                    $resultMap[$bid]['sim_donnee'] = $allocs[$bid];
                    $resultMap[$bid]['sim_restant'] = $stockFinal;
                }
            } else {
                // fallback: priorité (plus ancien)
                usort($bitems, function ($a, $b) {
                    if ($a['date_ts'] === $b['date_ts']) return $a['id'] <=> $b['id'];
                    return $a['date_ts'] <=> $b['date_ts'];
                });
                foreach ($bitems as $item) {
                    $bid = $item['id'];
                    $needInitial = $item['initial'];
                    $alloc = min($needInitial, $avail);
                    $resultMap[$bid]['sim_donnee'] = $alloc;
                    $avail -= $alloc;
                    $resultMap[$bid]['sim_restant'] = $avail;
                }
            }
        }

        // For besoins that don't belong to any group (no matching dons), leave sim_restant as 0 and sim_donnee 0

        // build result preserving ordre
        $result = [];
        if (is_array($besoins)) {
            foreach ($besoins as $b) {
                $bid = (int)($b['id'] ?? 0);
                if (isset($resultMap[$bid])) $result[] = $resultMap[$bid];
            }
        }

        // debug log
        @file_put_contents('/tmp/simulate.log', date('c') . ' ' . json_encode(['availableGroup' => $availableGroup, 'result_sample' => array_slice($result, 0, 5)]) . PHP_EOL, FILE_APPEND | LOCK_EX);

        return ['result' => $result, 'available_group' => $availableGroup];
    }
}
