<?php
$pageTitle   = 'Nouvel adhérent';
$currentPage = 'adherents';
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
require ROOT . '/views/layout/header.php';
?>

<div class="container container-sm">
    <div class="page-header">
        <div>
            <h1 class="page-title">Nouvel adhérent</h1>
            <p class="page-subtitle">Remplissez les informations de l'adhérent</p>
        </div>
        <a href="<?= BASE_URL ?>?page=adherents" class="btn btn-outline">← Retour</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>?page=adherents&action=store" id="form-adherent" novalidate>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="prenom" class="form-label">Prénom <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="prenom" 
                            name="prenom" 
                            class="form-input" 
                            value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                            placeholder="Mohamed"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label for="nom" class="form-label">Nom <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="nom" 
                            name="nom" 
                            class="form-input" 
                            value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                            placeholder="Benali"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="required">*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        placeholder="adresse@email.com"
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input 
                            type="tel" 
                            id="telephone" 
                            name="telephone" 
                            class="form-input" 
                            value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                            placeholder="0661-XXX-XXX"
                        >
                    </div>
                    <div class="form-group">
                        <label for="date_naissance" class="form-label">Date de naissance <span class="required">*</span></label>
                        <input 
                            type="date" 
                            id="date_naissance" 
                            name="date_naissance" 
                            class="form-input"
                            value="<?= htmlspecialchars($old['date_naissance'] ?? '') ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="salle_id" class="form-label">Salle d'inscription <span class="required">*</span></label>
                    <select id="salle_id" name="salle_id" class="form-select" required>
                        <option value="">— Choisir une salle —</option>
                        <?php foreach ($salles as $salle): ?>
                        <option value="<?= $salle['id'] ?>"
                            <?= (($old['salle_id'] ?? '') == $salle['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($salle['nom']) ?> — <?= htmlspecialchars($salle['ville']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="<?= BASE_URL ?>?page=adherents" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer l'adhérent</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require ROOT . '/views/layout/footer.php'; ?>
