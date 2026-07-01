<?php
$pageTitle   = 'Nouvel abonnement';
$currentPage = 'abonnements';
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
require ROOT . '/views/layout/header.php';
?>

<div class="container container-sm">
    <div class="page-header">
        <div>
            <h1 class="page-title">Nouvel abonnement</h1>
            <p class="page-subtitle">Associer un abonnement à un adhérent</p>
        </div>
        <a href="<?= BASE_URL ?>?page=abonnements" class="btn btn-outline">← Retour</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>?page=abonnements&action=store" id="form-abonnement">

                <div class="form-group">
                    <label for="adherent_id" class="form-label">Adhérent <span class="required">*</span></label>
                    <select id="adherent_id" name="adherent_id" class="form-select" required>
                        <option value="">— Sélectionner un adhérent —</option>
                        <?php foreach ($adherents as $a): ?>
                        <option value="<?= $a['id'] ?>"
                            <?= (($old['adherent_id'] ?? '') == $a['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['prenom'] . ' ' . strtoupper($a['nom'])) ?>
                            — <?= htmlspecialchars($a['email']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Type d'abonnement <span class="required">*</span></label>
                    <div class="type-selector">
                        <?php 
                        $typeIcons = ['Mensuel' => '📅', 'Trimestriel' => '📆', 'Annuel' => '🗓️'];
                        foreach ($types as $t): 
                            $icon = $typeIcons[$t['libelle']] ?? '🎫';
                        ?>
                        <label class="type-radio" id="type-label-<?= $t['id'] ?>">
                            <input type="radio" name="type_id" value="<?= $t['id'] ?>"
                                   <?= (($old['type_id'] ?? '') == $t['id']) ? 'checked' : '' ?>
                                   required>
                            <div class="type-radio-card">
                                <span class="type-radio-icon"><?= $icon ?></span>
                                <span class="type-radio-name"><?= htmlspecialchars($t['libelle']) ?></span>
                                <span class="type-radio-duration"><?= $t['duree_jours'] ?> jours</span>
                                <span class="type-radio-price"><?= number_format($t['prix'], 2) ?> MAD</span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date_debut" class="form-label">Date de début</label>
                    <input 
                        type="date" 
                        id="date_debut" 
                        name="date_debut" 
                        class="form-input"
                        value="<?= htmlspecialchars($old['date_debut'] ?? date('Y-m-d')) ?>"
                    >
                    <small class="form-hint">La date de fin sera calculée automatiquement selon le type.</small>
                </div>

                <div class="form-actions">
                    <a href="<?= BASE_URL ?>?page=abonnements" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer l'abonnement</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require ROOT . '/views/layout/footer.php'; ?>
