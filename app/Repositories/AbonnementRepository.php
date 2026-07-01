<?php
// ============================================================
// app/Repositories/AbonnementRepository.php
// Accès aux données pour les abonnements
// ============================================================

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Abonnement;
use PDO;

class AbonnementRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les abonnements avec détails adhérent et type
     */
    public function findAll(): array
    {
        $sql = "
            SELECT 
                ab.*,
                a.nom AS adherent_nom,
                a.prenom AS adherent_prenom,
                ta.libelle AS type_libelle,
                ta.prix AS type_prix
            FROM abonnements ab
            JOIN adherents a ON a.id = ab.adherent_id
            JOIN types_abonnement ta ON ta.id = ab.type_id
            ORDER BY ab.created_at DESC
        ";
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Récupère un abonnement par son ID
     */
    public function findById(int $id): ?Abonnement
    {
        $stmt = $this->pdo->prepare("SELECT * FROM abonnements WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row  = $stmt->fetch();

        return $row ? Abonnement::fromArray($row) : null;
    }

    /**
     * Récupère l'abonnement actif d'un adhérent (s'il existe)
     */
    public function findAbonnementActif(int $adherentId): ?Abonnement
    {
        $sql = "
            SELECT * FROM abonnements
            WHERE adherent_id = :id
              AND statut = 'actif'
              AND date_fin >= CURDATE()
            ORDER BY date_debut DESC
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $adherentId]);
        $row  = $stmt->fetch();

        return $row ? Abonnement::fromArray($row) : null;
    }

    /**
     * Crée un nouvel abonnement
     */
    public function create(Abonnement $abonnement): int
    {
        $sql = "
            INSERT INTO abonnements (adherent_id, type_id, date_debut, date_fin, statut)
            VALUES (:adherent_id, :type_id, :date_debut, :date_fin, :statut)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':adherent_id' => $abonnement->getAdherentId(),
            ':type_id'     => $abonnement->getTypeId(),
            ':date_debut'  => $abonnement->getDateDebut(),
            ':date_fin'    => $abonnement->getDateFin(),
            ':statut'      => $abonnement->getStatut(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour le statut d'un abonnement
     */
    public function updateStatut(int $id, string $statut): bool
    {
        $stmt = $this->pdo->prepare("UPDATE abonnements SET statut = :statut WHERE id = :id");
        return $stmt->execute([':statut' => $statut, ':id' => $id]);
    }

    /**
     * Expire automatiquement les abonnements dont la date de fin est dépassée
     */
    public function expirerAbonnementsDepasses(): int
    {
        $sql  = "UPDATE abonnements SET statut = 'expiré' WHERE date_fin < CURDATE() AND statut = 'actif'";
        $stmt = $this->pdo->query($sql);
        return $stmt->rowCount();
    }

    /**
     * Récupère les types d'abonnement disponibles
     */
    public function findAllTypes(): array
    {
        return $this->pdo->query("SELECT * FROM types_abonnement ORDER BY duree_jours")->fetchAll();
    }

    /**
     * Récupère un type d'abonnement par ID
     */
    public function findTypeById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM types_abonnement WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row  = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Historique des abonnements d'un adhérent
     */
    public function findByAdherent(int $adherentId): array
    {
        $sql = "
            SELECT ab.*, ta.libelle AS type_libelle, ta.prix
            FROM abonnements ab
            JOIN types_abonnement ta ON ta.id = ab.type_id
            WHERE ab.adherent_id = :id
            ORDER BY ab.date_debut DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $adherentId]);
        return $stmt->fetchAll();
    }
}
