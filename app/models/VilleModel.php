<?php

namespace app\models;

use Flight;

class VilleModel
{

    /**
     * Get all cities.
     *
     * @return array
     */
    public static function getAllCities()
    {
        // Example query, replace with your actual database logic
        $query = "SELECT * FROM ville";
        return Flight::db()->query($query)->fetchAll();
    }

    /**
     * Get a city by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getCityById($id)
    {
        // Example query, replace with your actual database logic
        $query = "SELECT * FROM ville WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getCityByName($name)
    {
        // Example query, replace with your actual database logic
        $query = "SELECT * FROM ville WHERE nom = :name";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function addCity($name, $population,$sinistre,$x,$y)
    {
        // Example query, replace with your actual database logic
        $query = "INSERT INTO ville (nom, nbrPopulation, sinistre, x, y) VALUES (:name, :population, :sinistre, :x, :y)";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':nbrPopulation', $population, \PDO::PARAM_INT);
        $stmt->bindParam(':sinistre', $sinistre, \PDO::PARAM_INT);
        $stmt->bindParam(':x', $x, \PDO::PARAM_INT);
        $stmt->bindParam(':y', $y, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function updateCity($id, $name, $population, $sinistre, $x, $y)
    {
        // Example query, replace with your actual database logic
        $query = "UPDATE ville SET nom = :name, nbrPopulation = :population, sinistre = :sinistre, x = :x, y = :y WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':population', $population, \PDO::PARAM_INT);
        $stmt->bindParam(':sinistre', $sinistre, \PDO::PARAM_INT);
        $stmt->bindParam(':x', $x, \PDO::PARAM_INT);
        $stmt->bindParam(':y', $y, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function deleteCity($id)
    {
        // Example query, replace with your actual database logic
        $query = "DELETE FROM ville WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
