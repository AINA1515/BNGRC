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


    public static function addBesoin($idVille, $idDons, $quantite, $prixUnitaire, $date = null)
    {
        // idTypeDons removed from besoinsVille; it's derived from dons when needed
        $query = "INSERT INTO besoinsVille (idVille, idDons, quantite, prixUnitaire, date_) VALUES (:idVille, :idDons, :quantite, :prixUnitaire, :date_)";
        $stmt = Flight::db()->prepare($query);
        // bind values with proper types; prixUnitaire may be null or numeric
        $stmt->bindValue(':idVille', (int)$idVille, \PDO::PARAM_INT);
        $stmt->bindValue(':idDons', (int)$idDons, \PDO::PARAM_INT);
        $stmt->bindValue(':quantite', (int)$quantite, \PDO::PARAM_INT);
        if ($prixUnitaire === null || $prixUnitaire === '') {
            $stmt->bindValue(':prixUnitaire', null, \PDO::PARAM_NULL);
        } else {
            // store as string/decimal format
            $stmt->bindValue(':prixUnitaire', (string)$prixUnitaire, \PDO::PARAM_STR);
        }
        // date: provided by caller or default to now
        if ($date === null || trim($date) === '') {
            $stmt->bindValue(':date_', date('Y-m-d H:i:s'));
        } else {
            $ts = strtotime($date);
            $stmt->bindValue(':date_', $ts === false ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $ts));
        }

        $ok = $stmt->execute();
        if (!$ok) {
            // log diagnostic info for debugging
            $err = $stmt->errorInfo();
            $payload = [
                'time' => date('c'),
                'idVille' => $idVille,
                'idDons' => $idDons,
                'quantite' => $quantite,
                'prixUnitaire' => $prixUnitaire,
                'error' => $err
            ];
            @file_put_contents('/tmp/besoin_insert.log', json_encode($payload) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        return $ok;
    }

    public static function updateBesoin($id, $idVille, $idDons, $quantite, $prixUnitaire)
    {
        $query = "UPDATE besoinsVille SET idVille = :idVille, idDons = :idDons, quantite = :quantite, prixUnitaire = :prixUnitaire WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindValue(':id', (int)$id, \PDO::PARAM_INT);
        $stmt->bindValue(':idVille', (int)$idVille, \PDO::PARAM_INT);
        $stmt->bindValue(':idDons', (int)$idDons, \PDO::PARAM_INT);
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

        // build ville map
        $villeMap = [];
        $villes = VilleModel::getAllCities();
        if (is_array($villes)) {
            foreach ($villes as $v) {
                if (isset($v['id'])) {
                    $villeMap[$v['id']] = $v['nom'] ?? '';
                }
            }
        }

        // build dons raw list and mapping to group key (normalized name + type)
        $donsAll = DonsModel::getAllDonations();
        $donsMap = []; // id => name (fallback)
        $donIdToGroup = []; // id => groupKey (name::type)
        if (is_array($donsAll)) {
            foreach ($donsAll as $d) {
                if (!isset($d['id'])) continue;
                $did = (int)$d['id'];
                $name = isset($d['nom']) ? trim(mb_strtolower($d['nom'])) : '';
                $type = isset($d['idTypeDons']) ? (int)$d['idTypeDons'] : 0;
                $key = $name . '::' . $type;
                $donsMap[$did] = $d['nom'] ?? '';
                $donIdToGroup[$did] = $key;
            }
        }

        // get aggregated donation totals per group (sum of quantite)
        $aggregated = DonsModel::getAggregatedDonations();
        $groupTotals = []; // groupKey => ['quantite' => total, 'nom' => originalName]
        if (is_array($aggregated)) {
            foreach ($aggregated as $g) {
                $gname = isset($g['nom']) ? trim(mb_strtolower($g['nom'])) : '';
                $gidType = isset($g['idTypeDons']) ? (int)$g['idTypeDons'] : 0;
                $gkey = $gname . '::' . $gidType;
                $groupTotals[$gkey] = [
                    'quantite' => (int)($g['quantite'] ?? 0),
                    'nom' => $g['nom'] ?? ''
                ];
            }
        }

        // build historique sums per (ville, groupKey) by mapping historiqueDons.idDons -> groupKey
        // each historique row references a don id; join to dons and sum the don.quantite to compute how much
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
                $idDons = isset($b['idDons']) ? (int)$b['idDons'] : null;
                $initial = isset($b['quantite']) ? (int)$b['quantite'] : 0;

                // determine the groupKey for the besoin's don id and collect historical donated amount across that group for this ville
                $groupKey = null;
                if ($idDons !== null) {
                    $groupKey = $donIdToGroup[$idDons] ?? null;
                }

                // historical donated to this ville for the whole group (raw sum)
                $rawDonnee = 0;
                if ($groupKey !== null) {
                    $mapK = $idVille . '_' . $groupKey;
                    $rawDonnee = $donMap[$mapK] ?? 0;
                }

                // Compute remaining stock for the group after historical donations to this ville.
                // restant = max(group_total - rawDonnee, 0)
                if ($groupKey !== null && isset($groupTotals[$groupKey])) {
                    // Show the aggregated group total as the 'restant' displayed before simulation
                    $groupTotal = (int)$groupTotals[$groupKey]['quantite'];
                    $restant = $groupTotal;
                } else {
                    // fallback: remaining of besoin after historical donations
                    $restant = max($initial - $rawDonnee, 0);
                }

                // Displayed 'donnee' should be how much was actually delivered to this besoinVille,
                // capped at the besoin's initial quantite (Départ) as requested.
                $donnee = min($rawDonnee, $initial);

                $besoinVilles[] = [
                    'id' => $b['id'] ?? null,
                    'idVille' => $idVille,
                    'nomVille' => $villeMap[$idVille] ?? ($b['nomVille'] ?? ''),
                    'idDons' => $idDons,
                    // prefer group name if available, otherwise fallback to the specific don name
                    'nomDon' => ($groupKey !== null && isset($groupTotals[$groupKey])) ? ($groupTotals[$groupKey]['nom'] ?? ($donsMap[$idDons] ?? ($b['nomDon'] ?? ''))) : ($donsMap[$idDons] ?? ($b['nomDon'] ?? '')),
                    'quantite' => $initial,
                    'donnee' => $donnee,
                    'restant' => $restant,
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
    public static function simulateAllocation()
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

        // group besoins by groupKey and collect their metadata (id, initial, date)
        $besoinsByGroup = [];
        if (is_array($besoins)) {
            foreach ($besoins as $b) {
                $bid = (int)($b['id'] ?? 0);
                $donId = isset($b['idDons']) ? (int)$b['idDons'] : 0;
                $gkey = $donIdToGroup[$donId] ?? null;
                if ($gkey === null) continue;
                $dateRaw = $b['date_'] ?? null;
                // parse date to timestamp; treat invalid/missing dates as very new so they are filled last
                $ts = false;
                if (!empty($dateRaw)) $ts = strtotime($dateRaw);
                if ($ts === false || $ts === null) $ts = PHP_INT_MAX;
                if (!isset($besoinsByGroup[$gkey])) $besoinsByGroup[$gkey] = [];
                $besoinsByGroup[$gkey][] = [
                    'id' => $bid,
                    'initial' => $resultMap[$bid]['initial'] ?? 0,
                    'date_ts' => $ts,
                ];
            }
        }

        // allocate per group across its besoins (oldest besoins first by date_)
        foreach ($besoinsByGroup as $gkey => $bitems) {
            // sort besoins by date timestamp ascending (oldest first), tie-breaker by id
            usort($bitems, function ($a, $b) {
                if ($a['date_ts'] === $b['date_ts']) return $a['id'] <=> $b['id'];
                return $a['date_ts'] <=> $b['date_ts'];
            });
            $avail = $availableGroup[$gkey] ?? 0;
            $allocatedTotal = 0;
            foreach ($bitems as $item) {
                if ($avail <= 0) break;
                $bid = $item['id'];
                $needInitial = $item['initial'];
                $alloc = min($needInitial, $avail);
                $resultMap[$bid]['sim_donnee'] = $alloc;
                $avail -= $alloc;
                $allocatedTotal += $alloc;
            }
            // remaining group stock after allocation
            $remainingAfter = max(0, ($availableGroup[$gkey] ?? 0) - $allocatedTotal);
            foreach ($bitems as $item) {
                $bid = $item['id'];
                $resultMap[$bid]['sim_restant'] = $remainingAfter;
            }
            $availableGroup[$gkey] = $remainingAfter;
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
