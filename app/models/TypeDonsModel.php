<?php

namespace app\models;

use Flight;

class TypeDonsModel
{
    /**
     * Get all donation types.
     *
     * @return array
     */
    public static function getAllTypes()
    {
        $query = "SELECT * FROM typeDons";
        return Flight::db()->query($query)->fetchAll();
    }

    /**
     * Get a donation type by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getTypeById($id)
    {
        $query = "SELECT * FROM typeDons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get a donation type by name.
     *
     * @param string $name
     * @return array|null
     */
    public static function getTypeByName($name)
    {
        $query = "SELECT * FROM typeDons WHERE nom = :name";
        $stmt = Flight::db()->prepare($query);
        $stmt->execute([':name' => $name]);
        return $stmt->fetch();
    }
    /**
     * Add a new donation type.
     *
     * @param string $name
     * @return bool
     */
    public static function addType($name)
    {
        $query = "INSERT INTO typeDons (nom) VALUES (:name)";
        $stmt = Flight::db()->prepare($query);
        return $stmt->execute([':name' => $name]);
    }
    /**
     * Update an existing donation type.
     *
     * @param int $id
     * @param string $name
     * @return bool
     */
    public static function updateType($id, $name)
    {
        $query = "UPDATE typeDons SET nom = :name WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Delete a donation type.
     *
     * @param int $id
     * @return bool
     */
    public static function deleteType($id)
    {
        $query = "DELETE FROM typeDons WHERE id = :id";
        $stmt = Flight::db()->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}