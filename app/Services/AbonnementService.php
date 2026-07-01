<?php
// ============================================================
// app/Services/AbonnementService.php
// Logique métier pour les abonnements
// ============================================================

declare(strict_types=1);

namespace App\Services;

use App\Entities\Abonnement;
use App\Repositories\AbonnementRepository;
use App\Repositories\AdherentRepository;

class AbonnementService
{
    private AbonnementRepository $repo;
    private AdherentRepository   $adherentRepo;

    public function __construct(
        AbonnementRepository $repo,
        AdherentRepository   $adherentRepo
    ) {
        $this->repo         = $repo;
        $this->adherentRepo = $adherentRepo;
    }

    /**
     * Retourne tous les abonnements avec détails
     */
    public function listerAbonnements(): array
    {
        return $this->repo->findAll();
    }

    /**
     * Retourne tous les types d'abonnement disponibles
     */
    public function listerTypes(): array
    {
        return $this->repo->findAllTypes();
    }

    /**
     * Vérifie si un adhérent a un abonnement valide aujourd'hui
     * Règle métier centrale du projet
     */
    public function abonnementEstValide(int $adherentId): bool
    {
        $abonnement = $this->repo->findAbonnementActif($adherentId);
        if ($abonnement === null) {
            return false;
        }
        return $abonnement->estValide();
    }

    /**
     * Récupère l'abonnement actif d'un adhérent
     */
    public function getAbonnementActif(int $adherentId): ?Abonnement
    {
        return $this->repo->findAbonnementActif($adherentId);
    }

    /**
     * Crée un nouvel abonnement pour un adhérent
     * Règle : un seul abonnement actif à la fois
     */
    public function creerAbonnement(array $data): int
    {
        $adherentId = (int) $data['adherent_id'];
        $typeId     = (int) $data['type_id'];

        // Vérifier existence adhérent
        if ($this->adherentRepo->findById($adherentId) === null) {
            throw new \RuntimeException("Adhérent introuvable.");
        }

        // Vérifier unicité abonnement actif
        if ($this->abonnementEstValide($adherentId)) {
            throw new \RuntimeException(
                "Cet adhérent possède déjà un abonnement actif. Annulez-le avant d'en créer un nouveau."
            );
        }

        // Récupérer le type pour calculer la date de fin
        $type = $this->repo->findTypeById($typeId);
        if ($type === null) {
            throw new \RuntimeException("Type d'abonnement introuvable.");
        }

        $dateDebut = !empty($data['date_debut']) ? $data['date_debut'] : date('Y-m-d');
        $dateFin   = date('Y-m-d', strtotime("+{$type['duree_jours']} days", strtotime($dateDebut)));

        $abonnement = new Abonnement(
            adherentId: $adherentId,
            typeId:     $typeId,
            dateDebut:  $dateDebut,
            dateFin:    $dateFin,
            statut:     Abonnement::STATUT_ACTIF
        );

        return $this->repo->create($abonnement);
    }

    /**
     * Annule un abonnement actif
     */
    public function annulerAbonnement(int $id): void
    {
        $abonnement = $this->repo->findById($id);
        if ($abonnement === null) {
            throw new \RuntimeException("Abonnement introuvable.");
        }

        if ($abonnement->getStatut() !== Abonnement::STATUT_ACTIF) {
            throw new \RuntimeException("Seul un abonnement actif peut être annulé.");
        }

        $this->repo->updateStatut($id, Abonnement::STATUT_ANNULE);
    }

    /**
     * Met à jour automatiquement les abonnements expirés
     */
    public function mettreAJourExpires(): int
    {
        return $this->repo->expirerAbonnementsDepasses();
    }

    /**
     * Historique des abonnements d'un adhérent
     */
    public function historiqueAdherent(int $adherentId): array
    {
        return $this->repo->findByAdherent($adherentId);
    }
}
