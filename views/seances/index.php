<?php
$pageTitle   = 'Séances';
$currentPage = 'seances';
require ROOT . '/views/layout/header.php';
?>

<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Séances</h1>
            <p class="page-subtitle"><?= count($seances) ?> séance<?= count($seances) > 1 ? 's' : '' ?> affichée<?= count($seances) > 1 ? 's' : '' ?></p>
        </div>
        <a href="<?= BASE_URL ?>?page=seances&action=create" class="btn btn-primary">
            + Enregistrer une séance
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?php if (!empty($seances)): ?>
            <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Adhérent</th>
                        <th>Activité</th>
                        <th>Salle</th>
                        <th>Équipement</th>
                        <th>Date</th>
                        <th>Durée</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($seances as $s): ?>
                    <tr>
                        <td class="id-col"><?= $s['id'] ?></td>
                        <td>
                            <div class="member-info">
                                <span class="avatar"><?= strtoupper(substr($s['adherent_nom'], 0, 1)) ?></span>
                                <span><?= htmlspecialchars($s['adherent_nom']) ?></span>
                            </div>
                        </td>
                        <td><span class="badge badge-blue"><?= htmlspecialchars($s['activite_libelle']) ?></span></td>
                        <td><?= htmlspecialchars($s['salle_nom']) ?></td>
                        <td><?= htmlspecialchars($s['equipement_libelle'] ?? '—') ?></td>
                        <td><?= date('d/m/Y', strtotime($s['date_seance'])) ?></td>
                        <td><span class="badge badge-gray"><?= $s['duree_minutes'] ?> min</span></td>
                        <td class="notes-col"><?= htmlspecialchars($s['notes'] ?? '—') ?></td>
                        <td>
                            <form method="POST" action="<?= BASE_URL ?>?page=seances&action=delete"
                                  onsubmit="return confirm('Supprimer cette séance ?')">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
                <div class="empty-state-block">
                    <p class="empty-icon">🏋️</p>
                    <p>Aucune séance enregistrée pour le moment.</p>
                    <a href="<?= BASE_URL ?>?page=seances&action=create" class="btn btn-primary">Enregistrer la première séance</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require ROOT . '/views/layout/footer.php'; ?>
