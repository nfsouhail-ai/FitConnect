<?php
$pageTitle   = 'Abonnements';
$currentPage = 'abonnements';
require ROOT . '/views/layout/header.php';
?>

<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Abonnements</h1>
            <p class="page-subtitle"><?= count($abonnements) ?> abonnement<?= count($abonnements) > 1 ? 's' : '' ?> au total</p>
        </div>
        <a href="<?= BASE_URL ?>?page=abonnements&action=create" class="btn btn-primary">
            + Nouvel abonnement
        </a>
    </div>

    <!-- Types disponibles -->
    <div class="types-grid">
        <?php 
        $typeIcons = ['Mensuel' => '📅', 'Trimestriel' => '📆', 'Annuel' => '🗓️'];
        $typeColors = ['Mensuel' => 'blue', 'Trimestriel' => 'purple', 'Annuel' => 'gold'];
        foreach ($types as $t): 
            $icon  = $typeIcons[$t['libelle']]  ?? '🎫';
            $color = $typeColors[$t['libelle']] ?? 'blue';
        ?>
        <div class="type-card type-<?= $color ?>">
            <span class="type-icon"><?= $icon ?></span>
            <div class="type-info">
                <div class="type-name"><?= htmlspecialchars($t['libelle']) ?></div>
                <div class="type-details"><?= $t['duree_jours'] ?> jours — <strong><?= number_format($t['prix'], 2) ?> MAD</strong></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Liste abonnements -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (!empty($abonnements)): ?>
            <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Adhérent</th>
                        <th>Type</th>
                        <th>Prix</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($abonnements as $ab): ?>
                    <tr>
                        <td class="id-col"><?= $ab['id'] ?></td>
                        <td>
                            <div class="member-info">
                                <span class="avatar"><?= strtoupper(substr($ab['adherent_prenom'], 0, 1)) ?></span>
                                <span><?= htmlspecialchars($ab['adherent_prenom'] . ' ' . strtoupper($ab['adherent_nom'])) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($ab['type_libelle']) ?></td>
                        <td><?= number_format($ab['type_prix'], 2) ?> MAD</td>
                        <td><?= date('d/m/Y', strtotime($ab['date_debut'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($ab['date_fin'])) ?></td>
                        <td>
                            <?php
                            $badges = [
                                'actif'   => 'badge-green',
                                'expiré'  => 'badge-red',
                                'annulé'  => 'badge-gray',
                            ];
                            $cls = $badges[$ab['statut']] ?? 'badge-gray';
                            ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($ab['statut']) ?></span>
                        </td>
                        <td>
                            <?php if ($ab['statut'] === 'actif'): ?>
                            <form method="POST" action="<?= BASE_URL ?>?page=abonnements&action=cancel"
                                  onsubmit="return confirm('Annuler cet abonnement ?')">
                                <input type="hidden" name="id" value="<?= $ab['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-warning">Annuler</button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
                <div class="empty-state-block">
                    <p class="empty-icon">🎫</p>
                    <p>Aucun abonnement enregistré.</p>
                    <a href="<?= BASE_URL ?>?page=abonnements&action=create" class="btn btn-primary">Créer le premier abonnement</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require ROOT . '/views/layout/footer.php'; ?>
