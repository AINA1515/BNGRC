<?php

namespace app\controllers;

use flight\Engine;
use app\models\TypeDonsModel;

class TypeDonsController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllTypes()
    {
        return TypeDonsModel::getAllTypes();
    }

    public function getTypeById($id)
    {
        $type = TypeDonsModel::getTypeById($id);
        if ($type) {
            return $type;
        } else {
            return ['status' => 'error', 'message' => 'Type not found'];
        }
    }

    public function addType($name)
    {
        if (TypeDonsModel::addType($name)) {
            return ['status' => 'success', 'message' => 'Type added successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add type'];
        }
    }

    public function updateType($id, $name)
    {
        if (TypeDonsModel::updateType($id, $name)) {
            return ['status' => 'success', 'message' => 'Type updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update type'];
        }
    }

    public function deleteType($id)
    {
        if (TypeDonsModel::deleteType($id)) {
            return ['status' => 'success', 'message' => 'Type deleted successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete type'];
        }
    }
}
