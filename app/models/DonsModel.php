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

    public static function getDonationByName($name)
    {
        $query = "SELECT * FROM dons WHERE nom = :name";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':name' => $name]);
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
        $groups = [];
        if (!is_array($all)) return [];
        foreach ($all as $d) {
            $name = isset($d['nom']) ? trim(mb_strtolower($d['nom'])) : '';
            $type = isset($d['idTypeDons']) ? (int)$d['idTypeDons'] : 0;
            $key = $name . '::' . $type;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'id' => (int)$d['id'],
                    'nom' => $d['nom'],
                    'idTypeDons' => $type,
                    'quantite' => (int)($d['quantite'] ?? 0),
                    'prixUnitaire' => $d['prixUnitaire'] ?? null,
                ];
            } else {
                // pick smallest id as representative
                if ((int)$d['id'] < $groups[$key]['id']) $groups[$key]['id'] = (int)$d['id'];
                $groups[$key]['quantite'] += (int)($d['quantite'] ?? 0);
                // keep first prixUnitaire if existing
                if (empty($groups[$key]['prixUnitaire']) && !empty($d['prixUnitaire'])) $groups[$key]['prixUnitaire'] = $d['prixUnitaire'];
            }
        }
        // return as indexed array
        return array_values($groups);
    }

    public static function getDonationsByIdType($idType)
    {
        $query = "SELECT * FROM dons WHERE idTypeDons = :idType";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':idType' => (int)$idType]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function addDonation($name, $idTypeDons)
    {
        $query = "INSERT INTO dons (nom, idTypeDons) VALUES (:name, :idTypeDons)";
        $stmt = Flight::db()->prepare($query);
        return $stmt->execute([':name' => $name, ':idTypeDons' => (int)$idTypeDons]);
    }

    public static function updateDonation($id, $name, $idTypeDons)
    {
        $query = "UPDATE dons SET nom = :name, idTypeDons = :idTypeDons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        return $stmt->execute([':id' => (int)$id, ':name' => $name, ':idTypeDons' => (int)$idTypeDons]);
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
    public static function addMultiple(array $noms, array $types, array $quantites)
    {
        $count = min(count($noms), count($types), count($quantites));
        $inserted = 0;
        $failed = 0;
        $skipped = 0;
        $db = Flight::db();
        try {
            $db->beginTransaction();
            $query = "INSERT INTO dons (nom, idTypeDons, quantite) VALUES (:nom, :idTypeDons, :quantite)";
            $stmt = $db->prepare($query);
            for ($i = 0; $i < $count; $i++) {
                $nom = trim($noms[$i]);
                $type = isset($types[$i]) ? (int)$types[$i] : 0;
                $qte = isset($quantites[$i]) ? (int)$quantites[$i] : 0;
                // skip entries with no valid type to avoid FK errors
                if ($type <= 0) {
                    $skipped++;
                    continue;
                }

                $params = [
                    ':nom' => $nom,
                    ':idTypeDons' => $type,
                    ':quantite' => $qte
                ];
                if ($stmt->execute($params)) {
                    $inserted++;
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
}
