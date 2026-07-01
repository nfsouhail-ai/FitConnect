<?php
// ============================================================
// app/Repositories/AdherentRepository.php
// Accès aux données pour les adhérents (PDO uniquement)
// ============================================================

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Adherent;
use PDO;

class AdherentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les adhérents avec le nom de leur salle
     */
    public function findAll(): array
    {
        $sql = "
            SELECT a.*, s.nom AS salle_nom
            FROM adherents a
            JOIN salles s ON s.id = a.salle_id
            ORDER BY a.nom, a.prenom
        ";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => Adherent::fromArray($row), $rows);
    }

    /**
     * Récupère un adhérent par son identifiant
     */
    public function findById(int $id): ?Adherent
    {
        $sql  = "SELECT * FROM adherents WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row  = $stmt->fetch();

        return $row ? Adherent::fromArray($row) : null;
    }

    /**
     * Récupère un adhérent par son email
     */
    public function findByEmail(string $email): ?Adherent
    {
        $sql  = "SELECT * FROM adherents WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row  = $stmt->fetch();

        return $row ? Adherent::fromArray($row) : null;
    }

    /**
     * Insère un nouvel adhérent en base et retourne son ID
     */
    public function create(Adherent $adherent): int
    {
        $sql = "
            INSERT INTO adherents (nom, prenom, email, telephone, date_naissance, salle_id)
            VALUES (:nom, :prenom, :email, :telephone, :date_naissance, :salle_id)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'            => $adherent->getNom(),
            ':prenom'         => $adherent->getPrenom(),
            ':email'          => $adherent->getEmail(),
            ':telephone'      => $adherent->getTelephone(),
            ':date_naissance' => $adherent->getDateNaissance(),
            ':salle_id'       => $adherent->getSalleId(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour un adhérent existant
     */
    public function update(Adherent $adherent): bool
    {
        $sql = "
            UPDATE adherents
            SET nom = :nom,
                prenom = :prenom,
                email = :email,
                telephone = :telephone,
                date_naissance = :date_naissance,
                salle_id = :salle_id
            WHERE id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom'            => $adherent->getNom(),
            ':prenom'         => $adherent->getPrenom(),
            ':email'          => $adherent->getEmail(),
            ':telephone'      => $adherent->getTelephone(),
            ':date_naissance' => $adherent->getDateNaissance(),
            ':salle_id'       => $adherent->getSalleId(),
            ':id'             => $adherent->getId(),
        ]);
    }

    /**
     * Supprime un adhérent (si aucune contrainte FK ne l'en empêche)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM adherents WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Vérifie si l'adhérent possède des séances enregistrées
     */
    public function hasSeances(int $adherentId): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM seances WHERE adherent_id = :id");
        $stmt->execute([':id' => $adherentId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si l'adhérent possède un abonnement en cours
     */
    public function hasAbonnementActif(int $adherentId): bool
    {
        $sql  = "SELECT COUNT(*) FROM abonnements WHERE adherent_id = :id AND statut = 'actif'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $adherentId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Récupère tous les adhérents avec des infos enrichies (salle, abonnement actif)
     */
    public function findAllWithDetails(): array
    {
        $sql = "
            SELECT 
                a.*,
                s.nom AS salle_nom,
                ab.statut AS abonnement_statut,
                ab.date_fin AS abonnement_fin,
                ta.libelle AS type_abonnement
            FROM adherents a
            JOIN salles s ON s.id = a.salle_id
            LEFT JOIN abonnements ab ON ab.adherent_id = a.id AND ab.statut = 'actif'
            LEFT JOIN types_abonnement ta ON ta.id = ab.type_id
            ORDER BY a.nom, a.prenom
        ";
        return $this->pdo->query($sql)->fetchAll();
    }
}
