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
			// provide villes, modèles de dons et types pour le formulaire
			$villes = VilleModel::getAllCities();
			$modelesAll = (new \app\models\ModeleDonsModel())->getAllModeles();
			$types = TypeDonsModel::getAllTypes();

			$app->render('formBesoin', [
				'csp_nonce' => $app->get('csp_nonce'),
				'villes' => $villes,
				'modelesAll' => $modelesAll,
				'types' => $types,
				'sinistreChartData' => []
			]);
		});

	// Render the Dons form page
	$router->get('/formDons', function () use ($app) {
		$types = TypeDonsModel::getAllTypes();
		// Build besoin list via model helper
		$besoinVilles = BesoinVilleModel::getBesoinsForForm();
		// modele dons list
		$modelesAll = \app\models\ModeleDonsModel::getAllModeles();

		$app->render('formDons', [
			'csp_nonce' => $app->get('csp_nonce'),
			'types' => $types,
			'besoinVilles' => $besoinVilles,
			'modelesAll' => $modelesAll,
			'sinistreChartData' => []
		]);
	});

	// Also expose the model management page via the header link
	$router->get('/formModelDons', function () use ($app) {
		// Reuse the same view as /formDons which contains the "Créer un modèle de don" block
		$types = TypeDonsModel::getAllTypes();
		$besoinVilles = BesoinVilleModel::getBesoinsForForm();
		$modelesAll = \app\models\ModeleDonsModel::getAllModeles();

		$app->render('formModelDons', [
			'csp_nonce' => $app->get('csp_nonce'),
			'types' => $types,
			'besoinVilles' => $besoinVilles,
			'modelesAll' => $modelesAll,
			'sinistreChartData' => []
		]);
	});

	// Handle multiple donations from form
	$router->post('/dons/add-multiple', function () use ($app) {
		$data = $app->request()->data;
		// Prefer modele[] (IDs). For backward compatibility, accept nom[] (free-text names) and convert to modele IDs.
		$modeles = isset($data['modele']) ? $data['modele'] : [];
		$types = isset($data['type']) ? $data['type'] : [];
		$quantites = isset($data['quantite']) ? $data['quantite'] : [];
		$dates = isset($data['date']) ? $data['date'] : [];
		$prixUnitaires = isset($data['prix']) ? $data['prix'] : [];

		// If the form submitted free-text names (nom[]), map them to modele IDs.
		if (empty($modeles) && isset($data['nom'])) {
			$noms = $data['nom'];
			// Load existing models
			$existing = \app\models\ModeleDonsModel::getAllModeles();
			$nameToId = [];
			if (is_array($existing)) {
				foreach ($existing as $m) {
					if (!empty($m['nom'])) $nameToId[strtolower(trim($m['nom']))] = $m['id'];
				}
			}
			$modeles = [];
			// Convert each provided name to an ID (create modele if not found)
			foreach ($noms as $idx => $name) {
				$nm = trim((string)$name);
				if ($nm === '') { $modeles[] = 0; continue; }
				$key = strtolower($nm);
				if (isset($nameToId[$key])) {
					$modeles[] = $nameToId[$key];
					continue;
				}
				// Create new modele with the corresponding type if available
				$idType = isset($types[$idx]) ? (int)$types[$idx] : 0;
				$ok = \app\models\ModeleDonsModel::addModele($nm, $idType);
				if ($ok) {
					// get last insert id
					$last = (int)Flight::db()->lastInsertId();
					$modeles[] = $last;
					// update local map to avoid duplicate inserts
					$nameToId[$key] = $last;
				} else {
					$modeles[] = 0;
				}
			}
		}

		// Ensure types align with modele: if a modele is provided, infer its type from modeleDons
		$maxLen = max(count((array)$modeles), count((array)$types));
		for ($i = 0; $i < $maxLen; $i++) {
			// normalize existing type
			$types[$i] = isset($types[$i]) ? (int)$types[$i] : 0;
			$modeleId = isset($modeles[$i]) ? (int)$modeles[$i] : 0;
			if ($modeleId > 0) {
				$modeleRow = \app\models\ModeleDonsModel::getModeleById($modeleId);
				if ($modeleRow && isset($modeleRow['idTypeDons'])) {
					$types[$i] = (int)$modeleRow['idTypeDons'];
				}
			}
		}

		$donsController = new DonsController($app);
		$result = $donsController->addMultiple($modeles, $types, $quantites, $dates, $prixUnitaires);
		// Redirect back with summary counts so UI can show feedback
		$inserted = isset($result['inserted']) ? (int)$result['inserted'] : 0;
		$failed = isset($result['failed']) ? (int)$result['failed'] : 0;
		$skipped = isset($result['skipped']) ? (int)$result['skipped'] : 0;
		$app->redirect('/formDons?inserted=' . $inserted . '&failed=' . $failed . '&skipped=' . $skipped);
	});

	// Add a new modele de don
	$router->post('/dons/add-model', function () use ($app) {
		$name = trim($app->request()->data->model_nom ?? '');
		$idType = (int)($app->request()->data->model_type ?? 0);
		if ($name === '' || $idType <= 0) {
			// Redirect back to the referrer or /formDons if missing
			$ref = $_SERVER['HTTP_REFERER'] ?? '/formDons';
			$app->redirect($ref . (strpos($ref, '?') === false ? '?' : '&') . 'model_added=0');
			return;
		}
		$prix = $app->request()->data->model_prix ?? null;
		$ok = \app\models\ModeleDonsModel::addModele($name, $idType, $prix);
		$ref = $_SERVER['HTTP_REFERER'] ?? '/formDons';
		$app->redirect($ref . (strpos($ref, '?') === false ? '?' : '&') . 'model_added=' . ($ok ? 1 : 0));
	});

	// Handle multiple besoins from form
	$router->post('/besoinVille/add-multiple', function () use ($app) {
		$data = $app->request()->data;
		$ville = isset($data['ville']) ? $data['ville'] : null;
		$modeles = isset($data['modeleDon']) ? $data['modeleDon'] : [];
		$quantites = isset($data['quantite']) ? $data['quantite'] : [];
		$pus = isset($data['pu']) ? $data['pu'] : [];
		$dates = isset($data['date']) ? $data['date'] : [];

		// Validate ville
		if (empty($ville) || (int)$ville <= 0) {
			@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'error' => 'missing_ville', 'payload' => (array)$data]) . PHP_EOL, FILE_APPEND | LOCK_EX);
			$app->redirect('/formBesoin?inserted=0&skipped=' . count((array)$modeles) . '&error=missing_ville');
			return;
		}
		$inserted = 0;
		$skipped = 0;
		foreach ($modeles as $idx => $idModele) {
			$qte = isset($quantites[$idx]) ? (int)$quantites[$idx] : 0;
			$pu = isset($pus[$idx]) ? $pus[$idx] : null;
			if (!$idModele) {
				@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'reason' => 'missing_modele', 'index' => $idx, 'payload' => ['qte' => $qte, 'pu' => $pu]]) . PHP_EOL, FILE_APPEND | LOCK_EX);
				$skipped++;
				continue;
			}
			// Si le PU n'est pas renseigné, utiliser celui du modèle
			if ($pu === null || $pu === '' || $pu == 0) {
				$modele = \app\models\ModeleDonsModel::getModeleById($idModele);
				if ($modele && isset($modele['prixUnitaire'])) {
					$pu = $modele['prixUnitaire'];
				}
			}
			$dateVal = isset($dates[$idx]) ? $dates[$idx] : null;
			$ok = BesoinVilleModel::addBesoin((int)$ville, (int)$idModele, $qte, $pu, $dateVal);
			if (!$ok) {
				@file_put_contents('/tmp/besoin_insert.log', json_encode(['time' => date('c'), 'reason' => 'insert_failed', 'index' => $idx, 'params' => ['ville' => (int)$ville, 'idModeleDons' => (int)$idModele, 'qte' => $qte, 'pu' => $pu]]) . PHP_EOL, FILE_APPEND | LOCK_EX);
			}
			if ($ok) {
				$inserted++;
			} else {
				$skipped++;
			}
		}
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

	// Distribution UI and purchase endpoint
	$router->get('/distribution', function () use ($app) {
		$controller = new \app\controllers\DistributionController($app);
		$controller->index();
	});

	$router->post('/distribution/purchase', function () use ($app) {
		$controller = new \app\controllers\DistributionController($app);
		$controller->purchase();
	});

	// Entrepot UI and actions
	$router->get('/entrepot', function () use ($app) {
		$controller = new \app\controllers\EntrepotController();
		$controller->index();
	});

	$router->post('/entrepot/add', function () use ($app) {
		$controller = new \app\controllers\EntrepotController();
		$controller->addStock();
	});

	$router->post('/entrepot/set', function () use ($app) {
		$controller = new \app\controllers\EntrepotController();
		$controller->setStock();
	});

	// Seed entrepot from current donations (admin)
	$router->post('/entrepot/seed', function () use ($app) {
		header('Content-Type: application/json');
		$db = Flight::db();
		try {
			$db->beginTransaction();
			$agg = \app\models\DonsModel::getAggregatedDonations();
			$map = [];
			foreach ($agg as $g) {
				$map[(int)$g['idModeleDons']] = (int)$g['quantite'];
			}
			$modeles = \app\models\ModeleDonsModel::getAllModeles();
			foreach ($modeles as $m) {
				$id = (int)$m['id'];
				$q = isset($map[$id]) ? $map[$id] : 0;
				\app\models\EntrepotModel::setStock($id, $q);
			}
			$db->commit();
			header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/entrepot'));
			exit;
		} catch (\Exception $e) {
			$db->rollBack();
			error_log('entrepot seed error: ' . $e->getMessage());
			$app->halt(500, 'Failed to seed entrepot');
		}
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
	$mode = $_GET['mode'] ?? 'priorite';
	$sim = BesoinVilleModel::simulateAllocation($mode);
	echo json_encode($sim);
});

// Apply simulation: set entrepot stock to aggregated donations totals
$router->post('/simulation/apply', function () use ($app) {
	header('Content-Type: application/json');
	$db = Flight::db();
	$mode = $app->request()->data->mode ?? 'priorite';
	try {
		$db->beginTransaction();
		// First, ensure entrepot contains a row for every modele with aggregated donation totals
		$aggregated = DonsModel::getAggregatedDonations();
		$map = [];
		foreach ($aggregated as $g) {
			$map[(int)$g['idModeleDons']] = (int)$g['quantite'];
		}
		$modeles = \app\models\ModeleDonsModel::getAllModeles();
		foreach ($modeles as $m) {
			$mid = (int)$m['id'];
			$qty = isset($map[$mid]) ? $map[$mid] : 0;
			\app\models\EntrepotModel::setStock($mid, $qty);
		}

		// Run simulation to get allocation per besoin
		$sim = BesoinVilleModel::simulateAllocation($mode);
		$result = $sim['result'] ?? [];
		// For each besoin with allocation, create distribution and decrement entrepot
		foreach ($result as $row) {
			$alloc = (int)($row['sim_donnee'] ?? 0);
			if ($alloc <= 0) continue;
			$besoinId = (int)($row['id'] ?? 0);
			// Fetch besoin to get ville, modele, prix, initial
			$besoin = BesoinVilleModel::getBesoinsById($besoinId);
			if (!$besoin) continue;
			$idVille = $besoin['idVille'] ?? null;
			$idModele = $besoin['idModeleDons'] ?? null;
			$initial = (int)($besoin['quantite'] ?? 0);
			$prix = $besoin['prixUnitaire'] ?? null;
			// determine initial entrepot stock for this modele
			$ent = \app\models\EntrepotModel::getStockByModele($idModele);
			$initialEnt = $ent ? (int)$ent['quantite'] : 0;
			// Create distribution record (store initial entrepot stock)
			\app\models\DistributionModel::createDistribution([
				'idBesoins' => $besoinId,
				'idVille' => $idVille,
				'date_' => date('Y-m-d H:i:s'),
				'quantiteBesoinDepart' => $initial,
				'quantiteBesoinRestant' => max(0, $initial - $alloc),
				'quantiteDonsInitiale' => $initialEnt,
				'quantiteDonsDistribue' => $alloc,
				'prixUnitaire' => $prix
			]);
			// Decrement entrepot for this modele
			if ($idModele) {
				\app\models\EntrepotModel::removeStock($idModele, $alloc);
			}
		}
		$db->commit();
		echo json_encode(['success' => true]);
	} catch (\Exception $e) {
		$db->rollBack();
		error_log('simulation apply error: ' . $e->getMessage());
		echo json_encode(['success' => false, 'error' => $e->getMessage()]);
	}
});

// Render the dedicated Simulation page
$router->get('/simulation', function () use ($app) {
	$besoinVilles = BesoinVilleModel::getEnrichedBesoinsForDashboard();
	$app->render('simulation', [
		'csp_nonce' => $app->get('csp_nonce'),
		'besoinVilles' => $besoinVilles,
	]);
});
