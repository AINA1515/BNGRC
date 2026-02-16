<?php

use app\controllers\VilleController;
use app\controllers\VueBesoinsParVilleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) use ($app) {

	$router->get('/', function () use ($app) {

		$villeController = new VilleController($app);
		$vueBesoinVilleController = new VueBesoinsParVilleController($app);

		$labels = $villeController->getAllCityNames();
		$chartData = $villeController->getAllNbrSinistre();


		$customChartData = ["labels" => $labels, "data" => $chartData];

		$besoinVilles = $vueBesoinVilleController->getAllBesoinsParVille();


		$app->render('dashboard', [
			'csp_nonce' => $app->get('csp_nonce'),
			'sinistreChartData' => $customChartData,
			'besoinVilles' => $besoinVilles
		]);
	});

}, [SecurityHeadersMiddleware::class]);
