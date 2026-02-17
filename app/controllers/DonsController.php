<?php 

namespace app\controllers;

use flight\Engine;
use app\models\DonsModel;

class DonsController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllDonations()
    {
        $donations = DonsModel::getAllDonations();
        return $donations;
    }

    /**
     * Return donations normalized for the dashboard view (include type name and normalized keys)
     *
     * @return array
     */
    public function getAllForDashboard()
    {
        $donsRaw = DonsModel::getAggregatedDonations();
        $typeMap = [];
        $types = \app\models\TypeDonsModel::getAllTypes();
        if (is_array($types)) {
            foreach ($types as $t) {
                $typeMap[$t['id'] ?? $t[0]] = $t['nom'] ?? ($t['name'] ?? '');
            }
        }

        $dons = [];
        if (is_array($donsRaw)) {
            foreach ($donsRaw as $d) {
                $dons[] = [
                    'id' => $d['id'] ?? null,
                    'nom' => $d['nom'] ?? ($d[0] ?? ''),
                    'typeDon' => $typeMap[$d['idTypeDons'] ?? ($d[1] ?? null)] ?? '',
                    'quantite' => $d['quantite'] ?? ($d['qte'] ?? null),
                    'prixUnitaire' => $d['prixUnitaire'] ?? ($d['prix_unitaire'] ?? null)
                ];
            }
        }
        return $dons;
    }

    public function getDonationById($id)
    {
        $donation = DonsModel::getDonationById($id);
        if ($donation) {
            return $donation;
        } else {
            return ['status' => 'error', 'message' => 'Donation not found'];
        }
    }

    public function getDonationByName($name)
    {
        $donation = DonsModel::getDonationByName($name);
        if ($donation) {
            return $donation;
        } else {
            return ['status' => 'error', 'message' => 'Donation not found'];
        }
    }

    public function getDonationsByIdType($idType)
    {
        $donations = DonsModel::getDonationsByIdType($idType);
        if ($donations) {
            return $donations;
        } else {
            return ['status' => 'error', 'message' => 'No donations found for this type'];
        }
    }

    public function addDonation($name, $amount)
    {
        if (DonsModel::addDonation($name, $amount)) {
            return ['status' => 'success', 'message' => 'Donation added successfully'];
        } else {
            $this->app->halt(500, 'Failed to add donation');
        }
    }

    /**
     * Add multiple donations at once. Expects arrays: nom[], type[], quantite[]
     */
    public function addMultiple(array $noms, array $types, array $quantites)
    {
        return DonsModel::addMultiple($noms, $types, $quantites);
    }
}