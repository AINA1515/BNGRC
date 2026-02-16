<?php

namespace app\controllers;

use flight\Engine;
use app\models\VueDonsParVilleModel;

class VueDonsParVilleController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getAllDonsParVille()
    {
        $view = VueDonsParVilleModel::getView();
        if ($view) {
            return $view;
        } else {
            return ['status' => 'error', 'message' => 'Not found'];
        }
    }
}
