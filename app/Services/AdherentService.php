<?php
// ============================================================
// app/Services/AdherentService.php
// Logique métier pour les adhérents
// ============================================================

declare(strict_types=1);

namespace App\Services;

use App\Entities\Adherent;
use App\Repositories\AdherentRepository;

class AdherentService
{
    private AdherentRepository $repo;

    public function __construct(AdherentRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Retourne tous les adhérents avec détails enrichis
     */
    public function listerAdherents(): array
    {
        return $this->repo->findAllWithDetails();
    }

    /**
     * Récupère un adhérent ou lève une exception s'il n'existe pas
     */
    public function getAdherent(int $id): Adherent
    {
        $adherent = $this->repo->findById($id);
        if ($adherent === null) {
            throw new \RuntimeException("Adhérent introuvable (ID: $id).");
        }
        return $adherent;
    }

    /**
     * Crée un nouvel adhérent après validation métier
     */
    public function creerAdherent(array $data): int
    {
        $this->validerDonnees($data);

        // Vérifier unicité email
        if ($this->repo->findByEmail($data['email']) !== null) {
            throw new \InvalidArgumentException("Un adhérent avec l'email « {$data['email']} » existe déjà.");
        }

        $adherent = new Adherent(
            nom:           trim($data['nom']),
            prenom:        trim($data['prenom']),
            email:         strtolower(trim($data['email'])),
            dateNaissance: $data['date_naissance'],
            salleId:       (int) $data['salle_id'],
            telephone:     !empty($data['telephone']) ? trim($data['telephone']) : null
        );

        return $this->repo->create($adherent);
    }

    /**
     * Met à jour un adhérent existant
     */
    public function modifierAdherent(int $id, array $data): void
    {
        $adherent = $this->getAdherent($id);
        $this->validerDonnees($data);

        // Vérifier unicité email si changement
        if ($data['email'] !== $adherent->getEmail()) {
            if ($this->repo->findByEmail($data['email']) !== null) {
                throw new \InvalidArgumentException("Cet email est déjà utilisé par un autre adhérent.");
            }
        }

        $adherent->setNom(trim($data['nom']));
        $adherent->setPrenom(trim($data['prenom']));
        $adherent->setEmail(strtolower(trim($data['email'])));
        $adherent->setTelephone(!empty($data['telephone']) ? trim($data['telephone']) : null);
        $adherent->setDateNaissance($data['date_naissance']);
        $adherent->setSalleId((int) $data['salle_id']);

        $this->repo->update($adherent);
    }

    /**
     * Supprime un adhérent (règle : pas de séances ni d'abonnement actif)
     */
    public function supprimerAdherent(int $id): void
    {
        $this->getAdherent($id); // Vérifie l'existence

        if ($this->repo->hasSeances($id)) {
            throw new \RuntimeException("Impossible de supprimer cet adhérent : il possède des séances enregistrées.");
        }

        if ($this->repo->hasAbonnementActif($id)) {
            throw new \RuntimeException("Impossible de supprimer cet adhérent : il possède un abonnement en cours.");
        }

        $this->repo->delete($id);
    }

    /**
     * Validation des données saisies
     */
    private function validerDonnees(array $data): void
    {
        if (empty($data['nom']) || strlen(trim($data['nom'])) < 2) {
            throw new \InvalidArgumentException("Le nom doit contenir au moins 2 caractères.");
        }

        if (empty($data['prenom']) || strlen(trim($data['prenom'])) < 2) {
            throw new \InvalidArgumentException("Le prénom doit contenir au moins 2 caractères.");
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("L'adresse email n'est pas valide.");
        }

        if (empty($data['date_naissance'])) {
            throw new \InvalidArgumentException("La date de naissance est obligatoire.");
        }

        // L'adhérent doit avoir au moins 16 ans
        $naissance = new \DateTime($data['date_naissance']);
        $age = $naissance->diff(new \DateTime())->y;
        if ($age < 16) {
            throw new \InvalidArgumentException("L'adhérent doit avoir au moins 16 ans.");
        }

        if (empty($data['salle_id']) || (int) $data['salle_id'] < 1) {
            throw new \InvalidArgumentException("Vous devez sélectionner une salle d'inscription.");
        }
    }
}
