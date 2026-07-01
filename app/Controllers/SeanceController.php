<?php
// ============================================================
// app/Controllers/SeanceController.php
// Orchestre les services pour les séances (sans JS)
// ============================================================

declare(strict_types=1);

namespace App\Controllers;

use App\Services\SeanceService;
use App\Services\AbonnementService;

class SeanceController
{
    private SeanceService     $service;
    private AbonnementService $abonnementService;

    public function __construct(SeanceService $service, AbonnementService $abonnementService)
    {
        $this->service           = $service;
        $this->abonnementService = $abonnementService;
    }

    /**
     * GET /seances → Liste des séances récentes
     */
    public function index(): void
    {
        $seances   = $this->service->listerSeances(50);
        $activites = $this->service->listerActivites();
        require ROOT . '/views/seances/index.php';
    }

    /**
     * GET /seances/create → Formulaire d'enregistrement
     */
    public function create(): void
    {
        global $pdo;

        $adherents = $pdo->query("
            SELECT a.id, a.nom, a.prenom 
            FROM adherents a 
            ORDER BY a.nom, a.prenom
        ")->fetchAll();

        $salles      = $pdo->query("SELECT * FROM salles ORDER BY nom")->fetchAll();
        $activites   = $this->service->listerActivites();
        $equipements = $pdo->query("
            SELECT e.id, e.libelle, s.nom AS salle_nom
            FROM equipements e
            JOIN salles s ON s.id = e.salle_id
            ORDER BY s.nom, e.libelle
        ")->fetchAll();

        require ROOT . '/views/seances/create.php';
    }

    /**
     * POST /seances/store → Enregistre une séance
     */
    public function store(): void
    {
        $data = [
            'adherent_id'      => $_POST['adherent_id']      ?? '',
            'salle_id'         => $_POST['salle_id']         ?? '',
            'type_activite_id' => $_POST['type_activite_id'] ?? '',
            'equipement_id'    => !empty($_POST['equipement_id']) ? $_POST['equipement_id'] : null,
            'date_seance'      => $_POST['date_seance']      ?? date('Y-m-d'),
            'duree_minutes'    => $_POST['duree_minutes']    ?? '',
            'notes'            => $_POST['notes']            ?? '',
        ];

        try {
            $id = $this->service->enregistrerSeance($data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Séance enregistrée avec succès (ID : ' . $id . ').'];
            header('Location: ' . BASE_URL . '?page=seances');
            exit;
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => $e->getMessage()];
            $_SESSION['old']   = $data;
            header('Location: ' . BASE_URL . '?page=seances&action=create');
            exit;
        }
    }

    /**
     * POST /seances/delete → Supprime une séance
     */
    public function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $this->service->supprimerSeance($id);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Séance supprimée avec succès.'];
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => $e->getMessage()];
        }
        header('Location: ' . BASE_URL . '?page=seances');
        exit;
    }
}
