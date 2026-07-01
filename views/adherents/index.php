<?php
$pageTitle   = 'Adhérents';
$currentPage = 'adherents';
require ROOT . '/views/layout/header.php';
?>

<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Adhérents</h1>
            <p class="page-subtitle"><?= count($adherents) ?> adhérent<?= count($adherents) > 1 ? 's' : '' ?> dans le réseau</p>
        </div>
        <a href="<?= BASE_URL ?>?page=adherents&action=create" class="btn btn-primary">
            + Nouvel adhérent
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?php if (!empty($adherents)): ?>
            <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Salle</th>
                        <th>Abonnement</th>
                        <th>Fin d'abonnement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adherents as $a): ?>
                    <tr>
                        <td class="id-col"><?= $a['id'] ?></td>
                        <td>
                            <div class="member-info">
                                <span class="avatar"><?= strtoupper(substr($a['prenom'], 0, 1)) ?></span>
                                <span><?= htmlspecialchars($a['prenom'] . ' ' . strtoupper($a['nom'])) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td><?= htmlspecialchars($a['telephone'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($a['salle_nom']) ?></td>
                        <td>
                            <?php if ($a['abonnement_statut'] === 'actif'): ?>
                                <span class="badge badge-green"><?= htmlspecialchars($a['type_abonnement'] ?? 'Actif') ?></span>
                            <?php else: ?>
                                <span class="badge badge-red">Sans abonnement</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($a['abonnement_fin'])): ?>
                                <?= date('d/m/Y', strtotime($a['abonnement_fin'])) ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="<?= BASE_URL ?>?page=adherents&action=delete"
                                  onsubmit="return confirm('Supprimer cet adhérent ?')">
                                <input type="hidden" name="id" value="<?= $a['id'] ?>">
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
                    <p class="empty-icon">👥</p>
                    <p>Aucun adhérent enregistré pour le moment.</p>
                    <a href="<?= BASE_URL ?>?page=adherents&action=create" class="btn btn-primary">Ajouter le premier adhérent</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require ROOT . '/views/layout/footer.php'; ?>
