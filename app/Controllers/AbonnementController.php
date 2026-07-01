<?php
// ============================================================
// app/Controllers/AbonnementController.php
// Orchestre les services pour les abonnements
// ============================================================

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AbonnementService;

class AbonnementController
{
    private AbonnementService $service;

    public function __construct(AbonnementService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /abonnements → Liste tous les abonnements
     */
    public function index(): void
    {
        // Mise à jour automatique des abonnements expirés à chaque chargement
        $this->service->mettreAJourExpires();

        $abonnements = $this->service->listerAbonnements();
        $types       = $this->service->listerTypes();
        require ROOT . '/views/abonnements/index.php';
    }

    /**
     * GET /abonnements/create → Formulaire de création
     */
    public function create(): void
    {
        global $pdo;
        $adherents = $pdo->query("
            SELECT a.id, a.nom, a.prenom, a.email 
            FROM adherents a 
            ORDER BY a.nom, a.prenom
        ")->fetchAll();

        $types = $this->service->listerTypes();
        require ROOT . '/views/abonnements/create.php';
    }

    /**
     * POST /abonnements/store → Crée un abonnement
     */
    public function store(): void
    {
        $data = [
            'adherent_id' => $_POST['adherent_id'] ?? '',
            'type_id'     => $_POST['type_id']     ?? '',
            'date_debut'  => $_POST['date_debut']  ?? date('Y-m-d'),
        ];

        try {
            $id = $this->service->creerAbonnement($data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Abonnement créé avec succès (ID: ' . $id . ').'];
            header('Location: ' . BASE_URL . '?page=abonnements');
            exit;
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => $e->getMessage()];
            $_SESSION['old']   = $data;
            header('Location: ' . BASE_URL . '?page=abonnements&action=create');
            exit;
        }
    }

    /**
     * POST /abonnements/cancel → Annule un abonnement
     */
    public function cancel(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $this->service->annulerAbonnement($id);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Abonnement annulé.'];
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => $e->getMessage()];
        }
        header('Location: ' . BASE_URL . '?page=abonnements');
        exit;
    }
}
