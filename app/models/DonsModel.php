<?php

namespace app\models;

use Flight;

class DonsModel
{
    /**
     * Get all donations.
     *
     * @return array
     */
    public static function getAllDonations()
    {
        // Example query, replace with your actual database logic
        $query = "SELECT * FROM dons";
        return Flight::db()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get a donation by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getDonationById($id)
    {
        $query = "SELECT * FROM dons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':id' => (int)$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public static function getDonationByModele($idModeleDons)
    {
        $query = "SELECT * FROM dons WHERE idModeleDons = :idModeleDons";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':idModeleDons' => (int)$idModeleDons]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /**
     * Return donations aggregated by normalized name + idTypeDons.
     * For each group we pick the smallest id as representative and sum quantities.
     * @return array
     */
    public static function getAggregatedDonations()
    {
        $all = self::getAllDonations();
        // Charger les modèles de dons pour affichage du nom
        $modeles = \app\models\ModeleDonsModel::getAllModeles();
        $modeleMap = [];
        if (is_array($modeles)) {
            foreach ($modeles as $m) {
                $modeleMap[$m['id']] = $m['nom'];
            }
        }
        $groups = [];
        if (!is_array($all)) return [];
        foreach ($all as $d) {
            $idModele = isset($d['idModeleDons']) ? (int)$d['idModeleDons'] : 0;
            $type = isset($d['idTypeDons']) ? (int)$d['idTypeDons'] : 0;
            $key = $idModele . '::' . $type;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'id' => (int)$d['id'],
                    'idModeleDons' => $idModele,
                    'idTypeDons' => $type,
                    'quantite' => (int)($d['quantite'] ?? 0),
                    'prixUnitaire' => $d['prixUnitaire'] ?? null,
                    'date_' => isset($d['date_']) ? $d['date_'] : null,
                    'nomModele' => $modeleMap[$idModele] ?? '',
                ];
            } else {
                if ((int)$d['id'] < $groups[$key]['id']) $groups[$key]['id'] = (int)$d['id'];
                $groups[$key]['quantite'] += (int)($d['quantite'] ?? 0);
                if (empty($groups[$key]['prixUnitaire']) && !empty($d['prixUnitaire'])) $groups[$key]['prixUnitaire'] = $d['prixUnitaire'];
                if (!empty($d['date_'])) {
                    $existing = $groups[$key]['date_'] ?? null;
                    $existingTs = $existing ? strtotime($existing) : 0;
                    $newTs = strtotime($d['date_']);
                    if ($newTs !== false && $newTs > $existingTs) {
                        $groups[$key]['date_'] = $d['date_'];
                    }
                }
            }
        }
        return array_values($groups);
    }

    public static function getDonationsByIdType($idType)
    {
        $query = "SELECT * FROM dons WHERE idTypeDons = :idType";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':idType' => (int)$idType]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function addDonation($idModeleDons, $idTypeDons, $quantite = 0, $prixUnitaire = null, $date_ = null)
    {
        $query = "INSERT INTO dons (idModeleDons, idTypeDons, quantite, prixUnitaire, date_) VALUES (:idModeleDons, :idTypeDons, :quantite, :prixUnitaire, :date_)";
        $stmt = Flight::db()->prepare($query);
        $params = [
            ':idModeleDons' => (int)$idModeleDons,
            ':idTypeDons' => (int)$idTypeDons,
            ':quantite' => (int)$quantite,
            ':prixUnitaire' => $prixUnitaire,
            ':date_' => $date_ ?? date('Y-m-d H:i:s')
        ];
        $ok = $stmt->execute($params);
        if ($ok) {
            // Update entrepot so available stock reflects this donation
            if ($idModeleDons && (int)$quantite > 0) {
                try {
                    \app\models\EntrepotModel::addStock($idModeleDons, (int)$quantite);
                } catch (\Exception $ex) {
                    @file_put_contents('/tmp/entrepot_add_error.log', $ex->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
                }
            }
        }
        return $ok;
    }

    public static function updateDonation($id, $idModeleDons, $idTypeDons, $quantite = 0, $prixUnitaire = null, $date_ = null)
    {
        $query = "UPDATE dons SET idModeleDons = :idModeleDons, idTypeDons = :idTypeDons, quantite = :quantite, prixUnitaire = :prixUnitaire, date_ = :date_ WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        return $stmt->execute([
            ':id' => (int)$id,
            ':idModeleDons' => (int)$idModeleDons,
            ':idTypeDons' => (int)$idTypeDons,
            ':quantite' => (int)$quantite,
            ':prixUnitaire' => $prixUnitaire,
            ':date_' => $date_ ?? date('Y-m-d H:i:s')
        ]);
    }

    public static function deleteDonation($id)
    {
        $query = "DELETE FROM dons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        return $stmt->execute([':id' => (int)$id]);
    }

    /**
     * Insert multiple donations in one transaction.
     * Arrays must be same length and values aligned by index.
     * Returns array with counts: ['inserted' => n, 'failed' => m]
     */
    public static function addMultiple(array $idModeleDons, array $types, array $quantites, array $dates = [], array $prixUnitaires = [])
    {
        $count = min(count($idModeleDons), count($types), count($quantites));
        $inserted = 0;
        $failed = 0;
        $skipped = 0;
        $db = Flight::db();
        try {
            $db->beginTransaction();
            $query = "INSERT INTO dons (idModeleDons, idTypeDons, quantite, prixUnitaire, date_) VALUES (:idModeleDons, :idTypeDons, :quantite, :prixUnitaire, :date_)";
            $stmt = $db->prepare($query);
            for ($i = 0; $i < $count; $i++) {
                $idModele = isset($idModeleDons[$i]) ? (int)$idModeleDons[$i] : 0;
                $type = isset($types[$i]) ? (int)$types[$i] : 0;
                $qte = isset($quantites[$i]) ? (int)$quantites[$i] : 0;
                $prixU = isset($prixUnitaires[$i]) ? $prixUnitaires[$i] : null;
                if ($type <= 0 || $idModele <= 0) {
                    $skipped++;
                    continue;
                }
                $dateParam = null;
                if (isset($dates[$i]) && trim($dates[$i]) !== '') {
                    $d = trim($dates[$i]);
                    $ts = strtotime($d);
                    if ($ts !== false) $dateParam = date('Y-m-d H:i:s', $ts);
                }
                if ($dateParam === null) {
                    $dateParam = date('Y-m-d H:i:s');
                }
                $params = [
                    ':idModeleDons' => $idModele,
                    ':idTypeDons' => $type,
                    ':quantite' => $qte,
                    ':prixUnitaire' => $prixU,
                    ':date_' => $dateParam
                ];
                if ($stmt->execute($params)) {
                    $inserted++;
                    // Also update entrepot to reflect new available stock
                    if ($idModele && $qte > 0) {
                        try {
                            \app\models\EntrepotModel::addStock($idModele, $qte);
                        } catch (\Exception $ex) {
                            // log and continue
                            @file_put_contents('/tmp/entrepot_add_error.log', $ex->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
                        }
                    }
                } else {
                    $failed++;
                }
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            return ['inserted' => $inserted, 'failed' => $failed, 'skipped' => $skipped, 'error' => $e->getMessage()];
        }
        return ['inserted' => $inserted, 'failed' => $failed, 'skipped' => $skipped];
    }

        /**
     * Get a donation by the name of the model (modeleDons.nom)
     * @param string $modelName
     * @return array|null
     */
    public static function getDonationByModelName($modelName)
    {
        $query = "SELECT d.* FROM dons d JOIN modeleDons m ON d.idModeleDons = m.id WHERE m.nom = :modelName LIMIT 1";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':modelName' => $modelName]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
}
