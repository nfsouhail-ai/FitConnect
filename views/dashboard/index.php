<?php
$pageTitle   = 'Dashboard';
$currentPage = 'dashboard';
require ROOT . '/views/layout/header.php';
?>

<div class="container">
    <!-- En-tête dashboard -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Tableau de bord</h1>
            <p class="page-subtitle">Vue d'ensemble du réseau FitConnect — <?= date('d/m/Y') ?></p>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-grid">
        <div class="stat-card stat-blue">
            <div class="stat-icon">👥</div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['total_adherents']) ?></div>
                <div class="stat-label">Adhérents</div>
            </div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon">🎫</div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['abonnements_actifs']) ?></div>
                <div class="stat-label">Abonnements actifs</div>
            </div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-icon">🏋️</div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['total_seances']) ?></div>
                <div class="stat-label">Séances enregistrées</div>
            </div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon">🏢</div>
            <div class="stat-body">
                <div class="stat-value">4</div>
                <div class="stat-label">Salles du réseau</div>
            </div>
        </div>
    </div>

    <!-- Graphiques / Listes -->
    <div class="dashboard-grid">

        <!-- Séances par salle -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Séances par salle</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($stats['seances_par_salle'])): ?>
                    <?php 
                    $maxSeances = max(array_column($stats['seances_par_salle'], 'total'));
                    $maxSeances = $maxSeances ?: 1;
                    foreach ($stats['seances_par_salle'] as $row): 
                        $pct = $maxSeances > 0 ? round(($row['total'] / $maxSeances) * 100) : 0;
                    ?>
                    <div class="bar-item">
                        <div class="bar-label">
                            <span><?= htmlspecialchars($row['salle']) ?></span>
                            <span class="bar-count"><?= $row['total'] ?></span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: <?= $pct ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">Aucune séance enregistrée.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activités populaires -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Activités les plus pratiquées</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($stats['seances_par_activite'])): ?>
                    <div class="activite-list">
                    <?php 
                    $rang = 0;
                    foreach ($stats['seances_par_activite'] as $row): 
                        if ($row['total'] == 0) continue;
                        $rang++;
                        $colors = ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#06b6d4', '#ef4444'];
                        $color  = $colors[($rang - 1) % count($colors)];
                    ?>
                        <div class="activite-item">
                            <span class="activite-badge" style="background:<?= $color ?>20;color:<?= $color ?>">
                                #<?= $rang ?>
                            </span>
                            <span class="activite-name"><?= htmlspecialchars($row['activite']) ?></span>
                            <span class="activite-count"><?= $row['total'] ?> séance<?= $row['total'] > 1 ? 's' : '' ?></span>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-state">Aucune donnée disponible.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Séances récentes -->
        <div class="card card-full">
            <div class="card-header">
                <h2 class="card-title">Séances récentes</h2>
                <a href="<?= BASE_URL ?>?page=seances" class="btn btn-sm btn-outline">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentSeances)): ?>
                <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Adhérent</th>
                            <th>Activité</th>
                            <th>Salle</th>
                            <th>Date</th>
                            <th>Durée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentSeances as $s): ?>
                        <tr>
                            <td><span class="avatar"><?= strtoupper(substr($s['adherent_nom'], 0, 1)) ?></span> <?= htmlspecialchars($s['adherent_nom']) ?></td>
                            <td><span class="badge badge-blue"><?= htmlspecialchars($s['activite_libelle']) ?></span></td>
                            <td><?= htmlspecialchars($s['salle_nom']) ?></td>
                            <td><?= date('d/m/Y', strtotime($s['date_seance'])) ?></td>
                            <td><?= $s['duree_minutes'] ?> min</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <?php else: ?>
                    <p class="empty-state p-4">Aucune séance enregistrée pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require ROOT . '/views/layout/footer.php'; ?>
