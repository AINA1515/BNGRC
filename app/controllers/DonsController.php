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