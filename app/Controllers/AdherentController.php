<?php
// ============================================================
// app/Controllers/AdherentController.php
// Orchestre les services et repositories pour les adhérents
// ============================================================

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AdherentService;
use App\Repositories\AdherentRepository;

class AdherentController
{
    private AdherentService    $service;
    private AdherentRepository $repo;

    public function __construct(AdherentService $service, AdherentRepository $repo)
    {
        $this->service = $service;
        $this->repo    = $repo;
    }

    /**
     * GET /adherents → Liste tous les adhérents
     */
    public function index(): void
    {
        $adherents = $this->service->listerAdherents();
        $salles    = $this->repo->findAll(); // pour d'autres usages
        require ROOT . '/views/adherents/index.php';
    }

    /**
     * GET /adherents/create → Formulaire de création
     */
    public function create(): void
    {
        $salles = $this->getSalles();
        require ROOT . '/views/adherents/create.php';
    }

    /**
     * POST /adherents/store → Enregistre un nouvel adhérent
     */
    public function store(): void
    {
        $data = [
            'nom'            => $_POST['nom']            ?? '',
            'prenom'         => $_POST['prenom']         ?? '',
            'email'          => $_POST['email']          ?? '',
            'telephone'      => $_POST['telephone']      ?? '',
            'date_naissance' => $_POST['date_naissance'] ?? '',
            'salle_id'       => $_POST['salle_id']       ?? '',
        ];

        try {
            $id = $this->service->creerAdherent($data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Adhérent créé avec succès (ID: ' . $id . ').'];
            header('Location: ' . BASE_URL . '?page=adherents');
            exit;
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => $e->getMessage()];
            $_SESSION['old']   = $data;
            header('Location: ' . BASE_URL . '?page=adherents&action=create');
            exit;
        }
    }

    /**
     * POST /adherents/delete → Supprime un adhérent
     */
    public function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $this->service->supprimerAdherent($id);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Adhérent supprimé avec succès.'];
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => $e->getMessage()];
        }
        header('Location: ' . BASE_URL . '?page=adherents');
        exit;
    }

    /**
     * Récupère la liste des salles depuis la base
     */
    private function getSalles(): array
    {
        global $pdo;
        return $pdo->query("SELECT * FROM salles ORDER BY nom")->fetchAll();
    }
}
