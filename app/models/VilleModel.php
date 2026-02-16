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
        $query = "SELECT * FROM ville WHERE name = :name";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function addCity($name, $population)
    {
        // Example query, replace with your actual database logic
        $query = "INSERT INTO ville (name, population) VALUES (:name, :population)";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':population', $population, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function updateCity($id, $name, $population)
    {
        // Example query, replace with your actual database logic
        $query = "UPDATE ville SET name = :name, population = :population WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->bindParam(':population', $population, \PDO::PARAM_INT);
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
