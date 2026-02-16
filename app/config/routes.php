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

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) use ($app) {

	$router->get('/', function () use ($app) {

		$villeController = new VilleController($app);
		$vueBesoinVilleController = new VueBesoinsParVilleController($app);
		$vueDonsParVilleController = new VueDonsParVilleController($app);

		$labels = $villeController->getAllCityNames();
		$chartData = $villeController->getAllNbrSinistre();


		$customChartData = ["labels" => $labels, "data" => $chartData];

		// Récupérer les besoins depuis la table besoinsVille (pour avoir idDons)
		$besoinsRaw = BesoinVilleModel::getAllBesoins();

		// Construire map des noms de villes et des noms de dons
		$villeMap = [];
		$villes = VilleModel::getAllCities();
		if (is_array($villes)) {
			foreach ($villes as $v) {
				if (isset($v['id'])) {
					$villeMap[$v['id']] = $v['nom'] ?? '';
				}
			}
		}

		$donsAll = DonsModel::getAllDonations();
		$donsMap = [];
		if (is_array($donsAll)) {
			foreach ($donsAll as $d) {
				if (isset($d['id'])) {
					$donsMap[$d['id']] = $d['nom'] ?? '';
				}
			}
		}

		// Calculer la quantité de dons déjà effectués par ville et par don (sommes des quantités des dons référencés dans historique)
		$donSumQuery = "SELECT h.idVille as idVille, h.idDons as idDons, SUM(d.quantite) as totalDonnee FROM historiqueDons h JOIN dons d on d.id = h.idDons GROUP BY h.idVille, h.idDons";
		$donSums = \Flight::db()->query($donSumQuery)->fetchAll(\PDO::FETCH_ASSOC);
		$donMap = [];
		foreach ($donSums as $s) {
			$idVilleSum = $s['idVille'] ?? null;
			$idDonsSum = $s['idDons'] ?? null;
			if ($idVilleSum !== null && $idDonsSum !== null) {
				$key = $idVilleSum . '_' . $idDonsSum;
				$donMap[$key] = (int) ($s['totalDonnee'] ?? 0);
			}
		}

		// Enrichir besoins avec nomVille, nomDon, quantite initiale, donnee et restant
		$besoinVilles = [];
		if (is_array($besoinsRaw)) {
			foreach ($besoinsRaw as $b) {
				$idVille = $b['idVille'] ?? null;
				$idDons = $b['idDons'] ?? null;
				$initial = isset($b['quantite']) ? (int)$b['quantite'] : 0;
				$key = $idVille . '_' . $idDons;
				$donnee = $donMap[$key] ?? 0;
				$restant = max($initial - $donnee, 0);

				$besoinVilles[] = [
					'idVille' => $idVille,
					'nomVille' => $villeMap[$idVille] ?? ($b['nomVille'] ?? ''),
					'idDons' => $idDons,
					'nomDon' => $donsMap[$idDons] ?? ($b['nomDon'] ?? ''),
					'quantite' => $initial,
					'donnee' => $donnee,
					'restant' => $restant,
					'prixUnitaire' => $b['prixUnitaire'] ?? null
				];
			}
		}

		// Récupère la liste des dons (disponibles) pour l'onglet Dons du dashboard
		$donsController = new DonsController($app);
		$donsRaw = $donsController->getAllDonations();

		// Construire un mapping idTypeDons -> nom du type
		$typeMap = [];
		$types = TypeDonsModel::getAllTypes();
		if (is_array($types)) {
			foreach ($types as $t) {
				$typeMap[$t['id'] ?? $t[0]] = $t['nom'] ?? ($t['name'] ?? '');
			}
		}

		// Normaliser les dons pour la vue
		$dons = [];
		if (is_array($donsRaw)) {
			foreach ($donsRaw as $d) {
				$dons[] = [
					'id' => $d['id'] ?? null,
					'nom' => $d['nom'] ?? ($d[0] ?? ''),
					'typeDon' => $typeMap[$d['idTypeDons'] ?? ($d[1] ?? null)] ?? '',
					'quantite' => $d['quantite'] ?? ($d['qte'] ?? null),
					'prixUnitaire' => $d['prixUnitaire'] ?? ($d['prix_unitaire'] ?? null)
				];
			}
		}


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
		$donsAll = DonsModel::getAllDonations();
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
		// Build a simple besoinVilles list for display in the form
		$besoinsRaw = BesoinVilleModel::getAllBesoins();
		$villes = VilleModel::getAllCities();
		$donsAll = DonsModel::getAllDonations();

		$villeMap = [];
		if (is_array($villes)) {
			foreach ($villes as $v) {
				$villeMap[$v['id']] = $v['nom'] ?? '';
			}
		}
		$donsMap = [];
		if (is_array($donsAll)) {
			foreach ($donsAll as $d) {
				$donsMap[$d['id']] = $d['nom'] ?? '';
			}
		}

		$besoinVilles = [];
		if (is_array($besoinsRaw)) {
			foreach ($besoinsRaw as $b) {
				$besoinVilles[] = [
					'ville' => $villeMap[$b['idVille'] ?? null] ?? ($b['nomVille'] ?? ''),
					'besoin' => $donsMap[$b['idDons'] ?? null] ?? ($b['nomDon'] ?? ''),
					'quantite' => $b['quantite'] ?? 0,
					'pu' => $b['prixUnitaire'] ?? null
				];
			}
		}

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
				@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'reason' => 'missing_don', 'index' => $idx, 'payload' => ['qte'=>$qte,'pu'=>$pu]]) . PHP_EOL, FILE_APPEND | LOCK_EX);
				$skipped++;
				continue;
			}

			// try to fetch the don to get its idTypeDons
			$don = DonsModel::getDonationById((int)$idDons);
			if (!$don || !isset($don['idTypeDons']) || empty($don['idTypeDons'])) {
				// cannot determine type for this don -> skip to avoid FK error
				@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'reason' => 'missing_don_type', 'index' => $idx, 'idDons' => $idDons, 'don' => $don]) . PHP_EOL, FILE_APPEND | LOCK_EX);
				$skipped++;
				continue;
			}

			// idType is no longer stored on besoins; simply insert with ville, idDons, quantite and prixUnitaire
			$ok = BesoinVilleModel::addBesoin((int)$ville, (int)$idDons, $qte, $pu);
			if (!$ok) {
				@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'reason' => 'insert_failed', 'index' => $idx, 'params' => ['ville'=>(int)$ville,'idDons'=>(int)$idDons,'qte'=>$qte,'pu'=>$pu]]) . PHP_EOL, FILE_APPEND | LOCK_EX);
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
	$router->get('/debug/dons-all', function() use ($app) {
		header('Content-Type: application/json');
		$d = \app\models\DonsModel::getAllDonations();
		echo json_encode(['count' => is_array($d) ? count($d) : 0, 'dons' => $d]);
	});

	// Debug: fetch a single donation by id
	$router->get('/debug/don/@id', function($id) use ($app) {
		header('Content-Type: application/json');
		$don = \app\models\DonsModel::getDonationById((int)$id);
		echo json_encode(['id' => (int)$id, 'don' => $don]);
	});
}, [SecurityHeadersMiddleware::class]);
