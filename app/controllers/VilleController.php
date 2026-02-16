<?php

namespace app\controllers;

use flight\Engine;
use app\models\VilleModel;

class VilleController
{
	protected Engine $app;

	public function __construct($app)
	{
		$this->app = $app;
	}

	public function getAllCities()
	{
		$cities = VilleModel::getAllCities();
		return $cities;
	}

	public function getAllCityNames()
	{
		$cityNames = VilleModel::getAllcityName();
		if ($cityNames) {
			return $cityNames;
		} else {
			return ['status' => 'error', 'message' => 'No cities found'];
		}
	}

	public function getAllNbrSinistre()
	{
		$sinistres = VilleModel::getAllNbrSinistre();
		if ($sinistres) {
			return $sinistres;
		} else {
			return ['status' => 'error', 'message' => 'No sinistres found'];
		}
	}

	public function getCityById($id)
	{
		$city = VilleModel::getCityById($id);
		if ($city) {
			return $city;
		} else {
			return ['status' => 'error', 'message' => 'City not found'];
		}
	}

	public function getCityByName($name)
	{
		$city = VilleModel::getCityByName($name);
		if ($city) {
			return $city;
		} else {
			return ['status' => 'error', 'message' => 'City not found'];
		}
	}

	public function addCity($name, $population, $sinistre, $x, $y)
	{
		if (VilleModel::addCity($name, $population, $sinistre, $x, $y)) {
			return ['status' => 'success', 'message' => 'City added successfully'];
		} else {
			$this->app->halt(500, 'Failed to add city');
		}
	}

	public function updateCity($id, $name, $population, $sinistre, $x, $y)
	{
		if (VilleModel::updateCity($id, $name, $population, $sinistre, $x, $y)) {
			return ['status' => 'success', 'message' => 'City updated successfully'];
		} else {
			$this->app->halt(500, 'Failed to update city');
		}
	}

	public function deleteCity($id)
	{
		if (VilleModel::deleteCity($id)) {
			return ['status' => 'success', 'message' => 'City deleted successfully'];
		} else {
			$this->app->halt(500, 'Failed to delete city');
		}
	}
}
