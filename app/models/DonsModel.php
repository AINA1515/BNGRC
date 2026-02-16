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
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getDonationByName($name)
    {
        $query = "SELECT * FROM dons WHERE nom = :name";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getDonationsByIdType($idType)
    {
        $query = "SELECT * FROM dons WHERE idTypeDons = :idType";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':idType', $idType, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function addDonation($name,$idTypeDons)
    {
        $query = "INSERT INTO dons (nom, idTypeDons) VALUES (:name, :idTypeDons)";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':idTypeDons', $idTypeDons, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function updateDonation($id, $name, $idTypeDons)
    {
        $query = "UPDATE dons SET nom = :name, idTypeDons = :idTypeDons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':idTypeDons', $idTypeDons, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function deleteDonation($id)
    {
        $query = "DELETE FROM dons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
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
