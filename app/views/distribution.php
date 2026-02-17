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
              <h4 class="card-title">Table de distribution</h4>
              <?php if (!empty($_GET['purchase']) && $_GET['purchase'] === 'insufficient') { ?>
                <div class="alert alert-danger">Fonds insuffisants pour effectuer l'achat.</div>
              <?php } elseif (!empty($_GET['purchase']) && $_GET['purchase'] === 'ok') { ?>
                <div class="alert alert-success">Achat effectué avec succès.</div>
              <?php } ?>
              <p><strong>Argent disponible :</strong> <?= number_format((float)($availableMoney ?? 0), 2) ?> </p>
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Ville</th>
                    <th>Libellé</th>
                    <th>Quantite</th>
                    <th>Acheter</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($distributions)) {
                    foreach ($distributions as $d) { ?>
                      <tr>
                        <td><?= (int)$d['id'] ?></td>
                        <td><?= $d['date_'] ?></td>
                        <td><?= htmlspecialchars($d['villeNom'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['modeleNom'] ?? '') ?></td>
                        <td>
                          <?php
                          // Find the current besoin for this distribution
                          $currentRestant = null;
                          if (!empty($besoins) && isset($d['idBesoins'])) {
                            foreach ($besoins as $b) {
                              if ((int)($b['id'] ?? 0) === (int)$d['idBesoins']) {
                                $currentRestant = (int)($b['quantite'] ?? 0);
                                break;
                              }
                            }
                          }
                          echo ($currentRestant !== null) ? $currentRestant : (int)($d['quantiteBesoinRestant'] ?? 0);
                          ?>
                        </td>
                        <td>
                          <?php if ((int)($d['quantiteBesoinRestant'] ?? 0) > 0) { ?>
                            <form action="<?= BASE_URL ?>/distribution/purchase" method="POST" class="d-flex gap-2">
                              <input type="hidden" name="distributionId" value="<?= (int)$d['id'] ?>">
                              <input type="number" name="quantiteToBuy" min="1" max="<?= (int)($d['quantiteBesoinRestant'] ?? 0) ?>" class="form-control" style="width:120px" required>
                              <input type="number" step="0.1" name="pourcentageAchat" class="form-control" placeholder="% frais" style="width:120px">
                              <button class="btn btn-sm btn-primary" type="submit">Acheter</button>
                            </form>
                          <?php } else { ?>
                            <span class="text-muted">Complet</span>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php }
                  } else { ?>
                    <tr>
                      <td colspan="7">Aucune distribution enregistrée</td>
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