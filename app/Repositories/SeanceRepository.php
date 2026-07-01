<?php
// ============================================================
// app/Repositories/SeanceRepository.php
// Accès aux données pour les séances
// ============================================================

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Seance;
use PDO;

class SeanceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Toutes les séances avec informations enrichies
     */
    public function findAll(int $limit = 50): array
    {
        $sql = "
            SELECT 
                s.*,
                CONCAT(a.prenom, ' ', UPPER(a.nom)) AS adherent_nom,
                sa.nom AS salle_nom,
                ta.libelle AS activite_libelle,
                e.libelle AS equipement_libelle
            FROM seances s
            JOIN adherents a ON a.id = s.adherent_id
            JOIN salles sa ON sa.id = s.salle_id
            JOIN types_activite ta ON ta.id = s.type_activite_id
            LEFT JOIN equipements e ON e.id = s.equipement_id
            ORDER BY s.date_seance DESC, s.created_at DESC
            LIMIT :limit
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère une séance par ID
     */
    public function findById(int $id): ?Seance
    {
        $stmt = $this->pdo->prepare("SELECT * FROM seances WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row  = $stmt->fetch();
        return $row ? Seance::fromArray($row) : null;
    }

    /**
     * Séances d'un adhérent spécifique
     */
    public function findByAdherent(int $adherentId): array
    {
        $sql = "
            SELECT 
                s.*,
                sa.nom AS salle_nom,
                ta.libelle AS activite_libelle,
                e.libelle AS equipement_libelle
            FROM seances s
            JOIN salles sa ON sa.id = s.salle_id
            JOIN types_activite ta ON ta.id = s.type_activite_id
            LEFT JOIN equipements e ON e.id = s.equipement_id
            WHERE s.adherent_id = :id
            ORDER BY s.date_seance DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $adherentId]);
        return $stmt->fetchAll();
    }

    /**
     * Enregistre une nouvelle séance
     */
    public function create(Seance $seance): int
    {
        $sql = "
            INSERT INTO seances 
                (adherent_id, salle_id, type_activite_id, equipement_id, date_seance, duree_minutes, notes)
            VALUES 
                (:adherent_id, :salle_id, :type_activite_id, :equipement_id, :date_seance, :duree_minutes, :notes)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':adherent_id'      => $seance->getAdherentId(),
            ':salle_id'         => $seance->getSalleId(),
            ':type_activite_id' => $seance->getTypeActiviteId(),
            ':equipement_id'    => $seance->getEquipementId(),
            ':date_seance'      => $seance->getDateSeance(),
            ':duree_minutes'    => $seance->getDureeMinutes(),
            ':notes'            => $seance->getNotes(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Supprime une séance
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM seances WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Statistiques : nombre de séances par salle
     */
    public function countBySalle(): array
    {
        $sql = "
            SELECT s.nom AS salle, COUNT(se.id) AS total
            FROM salles s
            LEFT JOIN seances se ON se.salle_id = s.id
            GROUP BY s.id, s.nom
            ORDER BY total DESC
        ";
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Statistiques : activités les plus pratiquées
     */
    public function countByActivite(): array
    {
        $sql = "
            SELECT ta.libelle AS activite, COUNT(s.id) AS total
            FROM types_activite ta
            LEFT JOIN seances s ON s.type_activite_id = ta.id
            GROUP BY ta.id, ta.libelle
            ORDER BY total DESC
        ";
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Récupère tous les types d'activité
     */
    public function findAllActivites(): array
    {
        return $this->pdo->query("SELECT * FROM types_activite ORDER BY libelle")->fetchAll();
    }

    /**
     * Récupère les équipements d'une salle
     */
    public function findEquipementsBySalle(int $salleId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM equipements WHERE salle_id = :id ORDER BY libelle");
        $stmt->execute([':id' => $salleId]);
        return $stmt->fetchAll();
    }

    /**
     * Nombre total de séances
     */
    public function countTotal(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM seances")->fetchColumn();
    }
}
