<?php

use app\controllers\ApiExampleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	$router->get('/', function() use ($app) {
		$labels = ['Ville 1', 'Ville 2', 'Ville 3', 'Ville 4 ', 'Ville 5', 'Ville 6'];
        $chartData = [1, 30, 5, 12, 10, 35];

        $customChartData = [
			'labels' => $labels,
			'data' => $chartData
		];

        $besoinVilles = [
            ["ville"=>"Ville 1 ", "besoin" => "Riz", "quantite" => "10kg", "pu" => "50000f"],
            ["ville"=>"Ville 2 ", "besoin" => "Haricot", "quantite" => "20kg", "pu" => "30000f"],
            ["ville"=>"Ville 3 ", "besoin" => "Maïs", "quantite" => "15kg", "pu" => "40000f"],
            ["ville"=>"Ville 4 ", "besoin" => "Blé", "quantite" => "25kg", "pu" => "45000f"],
            ["ville"=>"Ville 5 ", "besoin" => "Sorgho", "quantite" => "30kg", "pu" => "35000f"],
            ["ville"=>"Ville 6 ", "besoin" => "Millet", "quantite" => "12kg", "pu" => "20000f"]
        ];


		$app->render('dashboard', [
			'csp_nonce' => $app->get('csp_nonce'),
			'sinistreChartData' => $customChartData,
            'besoinVilles' => $besoinVilles
		]);
	});

	$router->get('/hello-world/@name', function($name) {
		echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	});

	$router->group('/api', function() use ($router) {
		$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
		$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
		$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	});
	
}, [ SecurityHeadersMiddleware::class ]);