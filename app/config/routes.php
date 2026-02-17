<?php

use app\controllers\VilleController;
use app\controllers\VueBesoinsParVilleController;
use app\models\BesoinVilleModel;
use app\models\VilleModel;
use app\models\DonsModel;
use app\controllers\VueDonsParVilleController;
use app\controllers\DonsController;
use app\models\TypeDonsModel;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// Ensure $app is available in this file scope (some environments inject it before including routes)
if (!isset($app)) {
	$app = \Flight::get('app') ?? null;
}

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) use ($app) {

	$router->get('/', function () use ($app) {

		$villeController = new VilleController($app);
		$vueBesoinVilleController = new VueBesoinsParVilleController($app);
		$vueDonsParVilleController = new VueDonsParVilleController($app);

		$labels = $villeController->getAllCityNames();
		$chartData = $villeController->getAllNbrSinistre();


		$customChartData = ["labels" => $labels, "data" => $chartData];

		// Build enriched besoins for dashboard using model helper
		$besoinVilles = BesoinVilleModel::getEnrichedBesoinsForDashboard();

		// Get normalized dons for dashboard
		$donsController = new DonsController($app);
		$dons = $donsController->getAllForDashboard();


		$app->render('dashboard', [
			'csp_nonce' => $app->get('csp_nonce'),

			'sinistreChartData' => $customChartData,
			'besoinVilles' => $besoinVilles,
			'dons' => $dons
		]);
	});

	// Render the Besoin form page
	$router->get('/formBesoin', function () use ($app) {
		// provide villes, dons and types for the form
		$villes = VilleModel::getAllCities();
		$donsAll = DonsModel::getAggregatedDonations();
		$types = TypeDonsModel::getAllTypes();

		$app->render('formBesoin', [
			'csp_nonce' => $app->get('csp_nonce'),
			'villes' => $villes,
			'donsAll' => $donsAll,
			'types' => $types,
			'sinistreChartData' => []
		]);
	});

	// Render the Dons form page
	$router->get('/formDons', function () use ($app) {
		$types = TypeDonsModel::getAllTypes();
		// Build besoin list via model helper
		$besoinVilles = BesoinVilleModel::getBesoinsForForm();

		$app->render('formDons', [
			'csp_nonce' => $app->get('csp_nonce'),
			'types' => $types,
			'besoinVilles' => $besoinVilles,
			'sinistreChartData' => []
		]);
	});

	// Handle multiple donations from form
	$router->post('/dons/add-multiple', function () use ($app) {
		$data = $app->request()->data;
		$noms = isset($data['nom']) ? $data['nom'] : [];
		$types = isset($data['type']) ? $data['type'] : [];
		$quantites = isset($data['quantite']) ? $data['quantite'] : [];
		$dates = isset($data['date']) ? $data['date'] : [];
		$donsController = new DonsController($app);
		$result = $donsController->addMultiple($noms, $types, $quantites);
		// Redirect back with summary counts so UI can show feedback
		$inserted = isset($result['inserted']) ? (int)$result['inserted'] : 0;
		$failed = isset($result['failed']) ? (int)$result['failed'] : 0;
		$skipped = isset($result['skipped']) ? (int)$result['skipped'] : 0;
		$app->redirect('/formDons?inserted=' . $inserted . '&failed=' . $failed . '&skipped=' . $skipped);
	});

	// Handle multiple besoins from form
	$router->post('/besoinVille/add-multiple', function () use ($app) {
		$data = $app->request()->data;
		$ville = isset($data['ville']) ? $data['ville'] : null;
	$dons = isset($data['don']) ? $data['don'] : [];
	$quantites = isset($data['quantite']) ? $data['quantite'] : [];
	$pus = isset($data['pu']) ? $data['pu'] : [];
	$dates = isset($data['date']) ? $data['date'] : [];

		// Validate ville
		if (empty($ville) || (int)$ville <= 0) {
			@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'error' => 'missing_ville', 'payload' => (array)$data]) . PHP_EOL, FILE_APPEND | LOCK_EX);
			$app->redirect('/formBesoin?inserted=0&skipped=' . count((array)$dons) . '&error=missing_ville');
			return;
		}
		// Insert each besoin row, deriving idTypeDons from the selected don to satisfy FK
		$inserted = 0;
		$skipped = 0;
		foreach ($dons as $idx => $idDons) {
			$qte = isset($quantites[$idx]) ? (int)$quantites[$idx] : 0;
			$pu = isset($pus[$idx]) ? $pus[$idx] : null;
			if (!$idDons) {
				@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'reason' => 'missing_don', 'index' => $idx, 'payload' => ['qte' => $qte, 'pu' => $pu]]) . PHP_EOL, FILE_APPEND | LOCK_EX);
				$skipped++;
				continue;
			}

			// Diagnostic: capture raw id and type before lookup
			$rawIdDons = $idDons;
			$idDonsInt = (int)$idDons;
			@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'debug' => 'before_lookup', 'index' => $idx, 'rawId' => $rawIdDons, 'castInt' => $idDonsInt, 'rawType' => gettype($rawIdDons)]) . PHP_EOL, FILE_APPEND | LOCK_EX);
			// try to fetch the don to get its idTypeDons
			$don = DonsModel::getDonationById($idDonsInt);
			@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'debug' => 'after_lookup', 'index' => $idx, 'idDonsInt' => $idDonsInt, 'don' => $don]) . PHP_EOL, FILE_APPEND | LOCK_EX);
			if (!$don || !isset($don['idTypeDons']) || empty($don['idTypeDons'])) {
				// cannot determine type for this don -> skip to avoid FK error
				@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'reason' => 'missing_don_type', 'index' => $idx, 'idDons' => $idDons, 'don' => $don]) . PHP_EOL, FILE_APPEND | LOCK_EX);
				$skipped++;
				continue;
			}

			// idType is no longer stored on besoins; simply insert with ville, idDons, quantite and prixUnitaire
			$dateVal = isset($dates[$idx]) ? $dates[$idx] : null;
			$ok = BesoinVilleModel::addBesoin((int)$ville, (int)$idDons, $qte, $pu, $dateVal);
			if (!$ok) {
				@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'reason' => 'insert_failed', 'index' => $idx, 'params' => ['ville' => (int)$ville, 'idDons' => (int)$idDons, 'qte' => $qte, 'pu' => $pu]]) . PHP_EOL, FILE_APPEND | LOCK_EX);
			}
			if ($ok) {
				$inserted++;
			} else {
				$skipped++;
			}
		}

		// Redirect back with a simple status query so the UI can show counts if desired
		$app->redirect('/formBesoin?inserted=' . $inserted . '&skipped=' . $skipped);
	});

	// Debug: list all donations
	$router->get('/debug/dons-all', function () use ($app) {
		header('Content-Type: application/json');
		$d = DonsModel::getAggregatedDonations();
		echo json_encode(['count' => is_array($d) ? count($d) : 0, 'dons' => $d]);
	});

	// Debug: fetch a single donation by id
	$router->get('/debug/don/@id', function ($id) use ($app) {
		header('Content-Type: application/json');
		$don = DonsModel::getDonationById((int)$id);
		echo json_encode(['id' => (int)$id, 'don' => $don]);
	});
}, [SecurityHeadersMiddleware::class]);

// Route to add a new type of donation
$router->post('/type/add', function () use ($app) {
	$name = trim($app->request()->data->name ?? '');
	if ($name === '') {
		$app->halt(400, 'Name required');
	}
	$ok = TypeDonsModel::addType($name);
	if ($ok) {
		$app->redirect('/formDons?type_added=1');
	} else {
		$app->halt(500, 'Failed to add type');
	}
});

// Simulation route: compute how much of each besoin would be filled by current dons/historique
$router->get('/simulate', function () use ($app) {
		header('Content-Type: application/json');
		$sim = BesoinVilleModel::simulateAllocation();
		echo json_encode($sim);
});
