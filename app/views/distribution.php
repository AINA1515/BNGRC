<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Distribution / Achat</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body>


  <body class="with-welcome-text">

    <div class="container-scroller">
      <?php include(ROOT_PATH . '/public/pages/header.php'); ?>

      <div class="main-panel">
        <div class="content-wrapper">

          <div class="card mt-3">
            <div class="card-body">
              <h4 class="card-title">Distribution et Achat de Besoins</h4>
              <?php if (!empty($_GET['purchase']) && $_GET['purchase'] === 'insufficient') { ?>
                <div class="alert alert-danger">
                  ❌ Fonds insuffisants pour effectuer l'achat.
                  <?php if (!empty($_GET['needed']) && !empty($_GET['available']) && !empty($_GET['shortfall'])) { ?>
                    <hr>
                    <strong>Détails financiers :</strong><br>
                    • Coût requis : <strong><?= htmlspecialchars($_GET['needed']) ?> €</strong><br>
                    • Argent disponible : <strong><?= htmlspecialchars($_GET['available']) ?> €</strong><br>
                    • Manque : <strong class="text-warning"><?= htmlspecialchars($_GET['shortfall']) ?> €</strong>
                  <?php } ?>
                </div>
              <?php } elseif (!empty($_GET['purchase']) && $_GET['purchase'] === 'ok') { ?>
                <div class="alert alert-success">✓ Achat effectué avec succès.</div>
              <?php } ?>
              
              <div class="alert alert-info">
                <strong>💰 Argent disponible :</strong> <?= number_format((float)($availableMoney ?? 0), 2) ?> €
              </div>

              <h5 class="mt-4">Besoins non satisfaits</h5>
              <table class="table table-striped">
                <thead class="table-light">
                  <tr>
                    <th>Besoin ID</th>
                    <th>Date</th>
                    <th>Ville</th>
                    <th>Modèle</th>
                    <th>P.U.</th>
                    <th>Quantité Restante</th>
                    <th>Coût Total (0%)</th>
                    <th>Action Achat</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $unsatisfiedCount = 0;
                  if (!empty($besoins)) {
                    foreach ($besoins as $b) { 
                      $remaining = (int)($b['quantite'] ?? 0);
                      
                      // Only show unsatisfied besoins (quantite > 0)
                      if ($remaining > 0) {
                        $unsatisfiedCount++;
                        $pu = (float)($b['prixUnitaire'] ?? 0);
                        $totalCost = $pu * $remaining;
                        $besoinId = (int)($b['id'] ?? 0);
                        $idVille = (int)($b['idVille'] ?? 0);
                        $idModele = (int)($b['idModeleDons'] ?? 0);
                        
                        // Get ville name
                        $villeName = $b['nomVille'] ?? '';
                        if (empty($villeName)) {
                          // Search in besoins enriched data
                          foreach ($besoins as $search) {
                            if ((int)($search['idVille'] ?? 0) === $idVille) {
                              $villeName = $search['nomVille'] ?? '';
                              break;
                            }
                          }
                        }
                        
                        // Get modele name
                        $modeleName = $b['nomDon'] ?? '';
                        if (empty($modeleName)) {
                          foreach ($modeles ?? [] as $m) {
                            if ((int)$m['id'] === $idModele) {
                              $modeleName = $m['nom'] ?? '';
                              break;
                            }
                          }
                        }
                  ?>
                      <tr>
                        <td><?= $besoinId ?></td>
                        <td><?= $b['date_'] ?? '' ?></td>
                        <td><strong><?= htmlspecialchars($villeName) ?></strong></td>
                        <td><?= htmlspecialchars($modeleName) ?></td>
                        <td><?= number_format($pu, 2) ?> €</td>
                        <td><span class="badge bg-warning"><?= $remaining ?></span></td>
                        <td><?= number_format($totalCost, 2) ?> €</td>
                        <td>
                          <form action="<?= BASE_URL ?>/distribution/purchase" method="POST" class="d-flex gap-1 flex-wrap">
                            <input type="hidden" name="besoinId" value="<?= $besoinId ?>">
                            <input type="number" name="quantiteToBuy" min="1" max="<?= $remaining ?>" value="<?= $remaining ?>" class="form-control form-control-sm" style="width:70px" required title="Quantité à acheter">
                            <input type="number" step="0.1" name="pourcentageAchat" class="form-control form-control-sm" placeholder="% frais" style="width:70px" title="Pourcentage de frais">
                            <button class="btn btn-sm btn-success" type="submit">Acheter</button>
                          </form>
                        </td>
                      </tr>
                    <?php }
                    }
                  }
                  if ($unsatisfiedCount === 0) { ?>
                    <tr>
                      <td colspan="8" class="text-center text-muted">✓ Tous les besoins sont satisfaits !</td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>

              <h5 class="mt-4">Historique des Distributions</h5>
              <table class="table table-sm table-secondary">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Ville</th>
                    <th>Modèle</th>
                    <th>Départ</th>
                    <th>Restant</th>
                    <th>Distribué</th>
                    <th>Stock Initial</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($distributions)) {
                    foreach ($distributions as $d) { 
                      // Only show if it's NOT fully satisfied
                      $currentRestant = null;
                      if (!empty($besoins) && isset($d['idBesoins'])) {
                        foreach ($besoins as $b) {
                          if ((int)($b['id'] ?? 0) === (int)$d['idBesoins']) {
                            $currentRestant = (int)($b['quantite'] ?? 0);
                            break;
                          }
                        }
                      }
                      // Show in history if it has been distributed
                      if (((int)($d['quantiteDonsDistribue'] ?? 0) > 0)) { 
                  ?>
                      <tr class="table-info">
                        <td><?= (int)$d['id'] ?></td>
                        <td><?= $d['date_'] ?></td>
                        <td><?= htmlspecialchars($d['villeNom'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['modeleNom'] ?? '') ?></td>
                        <td><?= (int)($d['quantiteBesoinDepart'] ?? 0) ?></td>
                        <td><?= ($currentRestant !== null) ? $currentRestant : (int)($d['quantiteBesoinRestant'] ?? 0) ?></td>
                        <td><strong><?= (int)($d['quantiteDonsDistribue'] ?? 0) ?></strong></td>
                        <td><?= (int)($d['quantiteDonsInitiale'] ?? 0) ?></td>
                      </tr>
                    <?php }
                    }
                  } else { ?>
                    <tr>
                      <td colspan="8" class="text-center text-muted">Aucune distribution enregistrée</td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/template.js"></script>
  </body>

</html>