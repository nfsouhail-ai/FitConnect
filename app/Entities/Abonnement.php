<?php
// ============================================================
// app/Entities/Abonnement.php
// Entité métier : Abonnement
// ============================================================

declare(strict_types=1);

namespace App\Entities;

class Abonnement
{
    private ?int    $id;
    private int     $adherentId;
    private int     $typeId;
    private string  $dateDebut;
    private string  $dateFin;
    private string  $statut;     // 'actif', 'expiré', 'annulé'
    private ?string $createdAt;

    // Libellés des statuts possibles
    public const STATUT_ACTIF    = 'actif';
    public const STATUT_EXPIRE   = 'expiré';
    public const STATUT_ANNULE   = 'annulé';

    public function __construct(
        int     $adherentId,
        int     $typeId,
        string  $dateDebut,
        string  $dateFin,
        string  $statut    = self::STATUT_ACTIF,
        ?int    $id        = null,
        ?string $createdAt = null
    ) {
        $this->adherentId = $adherentId;
        $this->typeId     = $typeId;
        $this->dateDebut  = $dateDebut;
        $this->dateFin    = $dateFin;
        $this->statut     = $statut;
        $this->id         = $id;
        $this->createdAt  = $createdAt;
    }

    // ── Accesseurs ───────────────────────────────────────────
    public function getId(): ?int        { return $this->id; }
    public function getAdherentId(): int { return $this->adherentId; }
    public function getTypeId(): int     { return $this->typeId; }
    public function getDateDebut(): string { return $this->dateDebut; }
    public function getDateFin(): string   { return $this->dateFin; }
    public function getStatut(): string    { return $this->statut; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // ── Mutateurs ────────────────────────────────────────────
    public function setStatut(string $statut): void { $this->statut = $statut; }
    public function setDateFin(string $date): void  { $this->dateFin = $date; }

    /**
     * Vérifie si l'abonnement est valide à une date donnée (format Y-m-d).
     * Règle métier : statut = 'actif' ET date du jour comprise entre début et fin.
     */
    public function estValide(string $dateJour = ''): bool
    {
        if ($dateJour === '') {
            $dateJour = date('Y-m-d');
        }

        return $this->statut === self::STATUT_ACTIF
            && $dateJour >= $this->dateDebut
            && $dateJour <= $this->dateFin;
    }

    /**
     * Crée une instance depuis un tableau associatif (résultat PDO)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            adherentId: (int) $data['adherent_id'],
            typeId:     (int) $data['type_id'],
            dateDebut:  $data['date_debut'],
            dateFin:    $data['date_fin'],
            statut:     $data['statut']      ?? self::STATUT_ACTIF,
            id:         isset($data['id']) ? (int) $data['id'] : null,
            createdAt:  $data['created_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'adherent_id' => $this->adherentId,
            'type_id'     => $this->typeId,
            'date_debut'  => $this->dateDebut,
            'date_fin'    => $this->dateFin,
            'statut'      => $this->statut,
        ];
    }
}
