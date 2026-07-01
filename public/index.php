<?php
// ============================================================
// public/index.php
// Point d'entrée unique de l'application FitConnect
// ============================================================

declare(strict_types=1);

// ── Constantes globales ──────────────────────────────────────
define('ROOT',     dirname(__DIR__));
define('BASE_URL', '/FitConnect/public/');

// ── Autoload manuel (pas de Composer) ───────────────────────
spl_autoload_register(function (string $class): void {
    // Convertit App\Entities\Adherent → app/Entities/Adherent.php
    $path = ROOT . '/' . str_replace(['\\', 'App/'], ['/', 'app/'], $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// ── Chargement de la configuration ──────────────────────────
require_once ROOT . '/config/Database.php';

// ── Session ──────────────────────────────────────────────────
session_start();

// ── Connexion PDO (disponible globalement) ───────────────────
try {
    $pdo = Database::getConnection();
} catch (\RuntimeException $e) {
    http_response_code(500);
    echo '<div style="font-family:sans-serif;padding:2rem;color:#c0392b;">';
    echo '<h1>Erreur de connexion</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    exit;
}

// ── Instanciation des couches ────────────────────────────────
use App\Repositories\AdherentRepository;
use App\Repositories\AbonnementRepository;
use App\Repositories\SeanceRepository;
use App\Services\AdherentService;
use App\Services\AbonnementService;
use App\Services\SeanceService;
use App\Controllers\AdherentController;
use App\Controllers\AbonnementController;
use App\Controllers\SeanceController;

$adherentRepo      = new AdherentRepository($pdo);
$abonnementRepo    = new AbonnementRepository($pdo);
$seanceRepo        = new SeanceRepository($pdo);

$adherentService   = new AdherentService($adherentRepo);
$abonnementService = new AbonnementService($abonnementRepo, $adherentRepo);
$seanceService     = new SeanceService($seanceRepo, $adherentRepo, $abonnementService);

$adherentCtrl      = new AdherentController($adherentService, $adherentRepo);
$abonnementCtrl    = new AbonnementController($abonnementService);
$seanceCtrl        = new SeanceController($seanceService, $abonnementService);

// ── Routeur simple ───────────────────────────────────────────
$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

try {
    switch ($page) {

        // Dashboard
        case 'dashboard':
            $stats = [
                'total_adherents'    => (int) $pdo->query("SELECT COUNT(*) FROM adherents")->fetchColumn(),
                'abonnements_actifs' => (int) $pdo->query("SELECT COUNT(*) FROM abonnements WHERE statut = 'actif'")->fetchColumn(),
                'total_seances'      => $seanceRepo->countTotal(),
                'seances_par_salle'  => $seanceRepo->countBySalle(),
                'seances_par_activite' => $seanceRepo->countByActivite(),
            ];
            $recentSeances = $seanceRepo->findAll(8);
            $currentPage   = 'dashboard';
            require ROOT . '/views/dashboard/index.php';
            break;

        // Adhérents
        case 'adherents':
            match ($action) {
                'create' => $adherentCtrl->create(),
                'store'  => $adherentCtrl->store(),
                'delete' => $adherentCtrl->delete(),
                default  => $adherentCtrl->index(),
            };
            break;

        // Abonnements
        case 'abonnements':
            match ($action) {
                'create' => $abonnementCtrl->create(),
                'store'  => $abonnementCtrl->store(),
                'cancel' => $abonnementCtrl->cancel(),
                default  => $abonnementCtrl->index(),
            };
            break;

        // Séances
        case 'seances':
            match ($action) {
                'create'      => $seanceCtrl->create(),
                'store'       => $seanceCtrl->store(),
                'delete'      => $seanceCtrl->delete(),
                default       => $seanceCtrl->index(),
            };
            break;

        // Page inconnue
        default:
            http_response_code(404);
            $currentPage = '';
            require ROOT . '/views/layout/header.php';
            echo '<div class="container"><div class="empty-state-block"><p class="empty-icon">🔍</p>';
            echo '<p>Page introuvable.</p>';
            echo '<a href="' . BASE_URL . '" class="btn btn-primary">Retour au dashboard</a></div></div>';
            require ROOT . '/views/layout/footer.php';
    }
} catch (\Exception $e) {
    // Gestion globale des erreurs
    error_log('[FitConnect] Erreur : ' . $e->getMessage());
    $currentPage = '';
    require ROOT . '/views/layout/header.php';
    echo '<div class="container"><div class="card" style="margin-top:2rem;">';
    echo '<div class="card-body"><h2 style="color:#ef4444">Une erreur est survenue</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<a href="' . BASE_URL . '" class="btn btn-outline" style="margin-top:1rem">← Retour</a></div></div></div>';
    require ROOT . '/views/layout/footer.php';
}
