<?php
$pageTitle   = 'Enregistrer une séance';
$currentPage = 'seances';
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
require ROOT . '/views/layout/header.php';
?>

<div class="container container-sm">
    <div class="page-header">
        <div>
            <h1 class="page-title">Enregistrer une séance</h1>
            <p class="page-subtitle">L'adhérent doit posséder un abonnement valide à la date du jour</p>
        </div>
        <a href="<?= BASE_URL ?>?page=seances" class="btn btn-outline">← Retour</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>?page=seances&action=store" id="form-seance">

                <div class="form-group">
                    <label for="adherent_id" class="form-label">Adhérent <span class="required">*</span></label>
                    <select id="adherent_id" name="adherent_id" class="form-select" required>
                        <option value="">— Sélectionner un adhérent —</option>
                        <?php foreach ($adherents as $a): ?>
                        <option value="<?= $a['id'] ?>"
                            <?= (($old['adherent_id'] ?? '') == $a['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['prenom'] . ' ' . strtoupper($a['nom'])) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="salle_id" class="form-label">Salle <span class="required">*</span></label>
                        <select id="salle_id" name="salle_id" class="form-select" required>
                            <option value="">— Sélectionner une salle —</option>
                            <?php foreach ($salles as $s): ?>
                            <option value="<?= $s['id'] ?>"
                                <?= (($old['salle_id'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_activite_id" class="form-label">Activité <span class="required">*</span></label>
                        <select id="type_activite_id" name="type_activite_id" class="form-select" required>
                            <option value="">— Choisir une activité —</option>
                            <?php foreach ($activites as $act): ?>
                            <option value="<?= $act['id'] ?>"
                                <?= (($old['type_activite_id'] ?? '') == $act['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($act['libelle']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="equipement_id" class="form-label">
                        Équipement <span class="text-muted">(optionnel)</span>
                    </label>
                    <select id="equipement_id" name="equipement_id" class="form-select">
                        <option value="">— Aucun équipement —</option>
                        <?php foreach ($equipements as $eq): ?>
                        <option value="<?= $eq['id'] ?>"
                            <?= (($old['equipement_id'] ?? '') == $eq['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($eq['libelle']) ?>
                            — <?= htmlspecialchars($eq['salle_nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_seance" class="form-label">Date <span class="required">*</span></label>
                        <input
                            type="date"
                            id="date_seance"
                            name="date_seance"
                            class="form-input"
                            value="<?= htmlspecialchars($old['date_seance'] ?? date('Y-m-d')) ?>"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label for="duree_minutes" class="form-label">Durée (minutes) <span class="required">*</span></label>
                        <input
                            type="number"
                            id="duree_minutes"
                            name="duree_minutes"
                            class="form-input"
                            value="<?= htmlspecialchars($old['duree_minutes'] ?? '60') ?>"
                            min="5"
                            max="480"
                            placeholder="60"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Notes <span class="text-muted">(optionnel)</span></label>
                    <textarea
                        id="notes"
                        name="notes"
                        class="form-textarea"
                        rows="3"
                        placeholder="Observations sur la séance..."
                    ><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="<?= BASE_URL ?>?page=seances" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer la séance</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require ROOT . '/views/layout/footer.php'; ?>
