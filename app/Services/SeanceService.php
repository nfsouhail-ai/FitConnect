<?php
// ============================================================
// app/Services/SeanceService.php
// Logique métier pour les séances
// ============================================================

declare(strict_types=1);

namespace App\Services;

use App\Entities\Seance;
use App\Repositories\SeanceRepository;
use App\Repositories\AdherentRepository;

class SeanceService
{
    private SeanceRepository  $repo;
    private AdherentRepository $adherentRepo;
    private AbonnementService  $abonnementService;

    public function __construct(
        SeanceRepository   $repo,
        AdherentRepository $adherentRepo,
        AbonnementService  $abonnementService
    ) {
        $this->repo              = $repo;
        $this->adherentRepo      = $adherentRepo;
        $this->abonnementService = $abonnementService;
    }

    /**
     * Retourne toutes les séances récentes
     */
    public function listerSeances(int $limit = 50): array
    {
        return $this->repo->findAll($limit);
    }

    /**
     * Séances d'un adhérent
     */
    public function seancesAdherent(int $adherentId): array
    {
        return $this->repo->findByAdherent($adherentId);
    }

    /**
     * Enregistre une nouvelle séance
     * Règle métier CRITIQUE : abonnement valide obligatoire
     */
    public function enregistrerSeance(array $data): int
    {
        $adherentId = (int) $data['adherent_id'];

        // Vérifier existence adhérent
        if ($this->adherentRepo->findById($adherentId) === null) {
            throw new \RuntimeException("Adhérent introuvable.");
        }

        // !! RÈGLE MÉTIER : abonnement valide à la date de la séance
        if (!$this->abonnementService->abonnementEstValide($adherentId)) {
            throw new \RuntimeException(
                "Impossible d'enregistrer la séance : l'adhérent ne possède pas d'abonnement valide à ce jour."
            );
        }

        // Validation des données
        $this->validerDonnees($data);

        $seance = new Seance(
            adherentId:     $adherentId,
            salleId:        (int) $data['salle_id'],
            typeActiviteId: (int) $data['type_activite_id'],
            dateSeance:     $data['date_seance'],
            dureeMinutes:   (int) $data['duree_minutes'],
            equipementId:   !empty($data['equipement_id']) ? (int) $data['equipement_id'] : null,
            notes:          !empty($data['notes']) ? trim($data['notes']) : null
        );

        return $this->repo->create($seance);
    }

    /**
     * Supprime une séance
     */
    public function supprimerSeance(int $id): void
    {
        if ($this->repo->findById($id) === null) {
            throw new \RuntimeException("Séance introuvable (ID: $id).");
        }
        $this->repo->delete($id);
    }

    /**
     * Statistiques pour le dashboard
     */
    public function statistiques(): array
    {
        return [
            'total_seances'    => $this->repo->countTotal(),
            'par_salle'        => $this->repo->countBySalle(),
            'par_activite'     => $this->repo->countByActivite(),
        ];
    }

    /**
     * Récupère tous les types d'activité
     */
    public function listerActivites(): array
    {
        return $this->repo->findAllActivites();
    }

    /**
     * Récupère les équipements d'une salle
     */
    public function equipementsSalle(int $salleId): array
    {
        return $this->repo->findEquipementsBySalle($salleId);
    }

    /**
     * Validation des données d'une séance
     */
    private function validerDonnees(array $data): void
    {
        if (empty($data['salle_id'])) {
            throw new \InvalidArgumentException("La salle est obligatoire.");
        }

        if (empty($data['type_activite_id'])) {
            throw new \InvalidArgumentException("Le type d'activité est obligatoire.");
        }

        if (empty($data['date_seance'])) {
            throw new \InvalidArgumentException("La date de séance est obligatoire.");
        }

        $duree = (int) ($data['duree_minutes'] ?? 0);
        if ($duree < 5 || $duree > 480) {
            throw new \InvalidArgumentException("La durée doit être comprise entre 5 et 480 minutes.");
        }
    }
}
