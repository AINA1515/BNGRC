<?php
// Simple list of entrepot stocks and a small form to add stock
?>

<div class="card">
  <div class="card-body">
    <h4>Entrepot</h4>
    <table class="table">
      <thead>
        <tr><th>Modele</th><th>Quantite</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach (($stocks?:[]) as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['modeleNom'] ?? ('#'.$s['idModeleDons'])) ?></td>
            <td><?= htmlspecialchars($s['quantite']) ?></td>
            <td>
              <form method="post" action="/entrepot/set">
                <input type="hidden" name="idModele" value="<?= htmlspecialchars($s['idModeleDons']) ?>" />
                <input type="number" name="quantite" value="<?= htmlspecialchars($s['quantite']) ?>" />
                <button class="btn btn-sm btn-primary">Set</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h5>Ajouter du stock</h5>
    <form method="post" action="/entrepot/add">
      <div class="form-group">
        <label>Modele</label>
        <select name="idModele" class="form-control">
          <?php foreach (($modeles?:[]) as $m): ?>
            <option value="<?= htmlspecialchars($m['id']) ?>"><?= htmlspecialchars($m['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Quantite</label>
        <input type="number" name="quantite" class="form-control" />
      </div>
      <button class="btn btn-success">Ajouter</button>
    </form>
    <form method="post" action="/entrepot/seed" style="margin-top:1rem">
      <button class="btn btn-secondary">Remplir l'entrepot depuis les dons actuels</button>
    </form>
  </div>
</div>
