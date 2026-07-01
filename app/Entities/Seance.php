<?php
// ============================================================
// app/Entities/Seance.php
// Entité métier : Séance
// ============================================================

declare(strict_types=1);

namespace App\Entities;

class Seance
{
    private ?int    $id;
    private int     $adherentId;
    private int     $salleId;
    private int     $typeActiviteId;
    private ?int    $equipementId;
    private string  $dateSeance;
    private int     $dureeMinutes;
    private ?string $notes;
    private ?string $createdAt;

    public function __construct(
        int     $adherentId,
        int     $salleId,
        int     $typeActiviteId,
        string  $dateSeance,
        int     $dureeMinutes,
        ?int    $equipementId = null,
        ?string $notes        = null,
        ?int    $id           = null,
        ?string $createdAt    = null
    ) {
        $this->adherentId     = $adherentId;
        $this->salleId        = $salleId;
        $this->typeActiviteId = $typeActiviteId;
        $this->dateSeance     = $dateSeance;
        $this->dureeMinutes   = $dureeMinutes;
        $this->equipementId   = $equipementId;
        $this->notes          = $notes;
        $this->id             = $id;
        $this->createdAt      = $createdAt;
    }

    // ── Accesseurs ───────────────────────────────────────────
    public function getId(): ?int            { return $this->id; }
    public function getAdherentId(): int     { return $this->adherentId; }
    public function getSalleId(): int        { return $this->salleId; }
    public function getTypeActiviteId(): int { return $this->typeActiviteId; }
    public function getEquipementId(): ?int  { return $this->equipementId; }
    public function getDateSeance(): string  { return $this->dateSeance; }
    public function getDureeMinutes(): int   { return $this->dureeMinutes; }
    public function getNotes(): ?string      { return $this->notes; }
    public function getCreatedAt(): ?string  { return $this->createdAt; }

    // ── Mutateurs ────────────────────────────────────────────
    public function setNotes(?string $notes): void       { $this->notes = $notes; }
    public function setEquipementId(?int $id): void      { $this->equipementId = $id; }
    public function setDureeMinutes(int $duree): void    { $this->dureeMinutes = $duree; }

    /**
     * Durée formatée lisiblement (ex: 1h30)
     */
    public function getDureeFormatee(): string
    {
        $heures  = intdiv($this->dureeMinutes, 60);
        $minutes = $this->dureeMinutes % 60;

        if ($heures > 0 && $minutes > 0) {
            return "{$heures}h{$minutes}min";
        } elseif ($heures > 0) {
            return "{$heures}h";
        }
        return "{$minutes}min";
    }

    public static function fromArray(array $data): self
    {
        return new self(
            adherentId:     (int) $data['adherent_id'],
            salleId:        (int) $data['salle_id'],
            typeActiviteId: (int) $data['type_activite_id'],
            dateSeance:     $data['date_seance'],
            dureeMinutes:   (int) $data['duree_minutes'],
            equipementId:   isset($data['equipement_id']) ? (int) $data['equipement_id'] : null,
            notes:          $data['notes'] ?? null,
            id:             isset($data['id']) ? (int) $data['id'] : null,
            createdAt:      $data['created_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'adherent_id'      => $this->adherentId,
            'salle_id'         => $this->salleId,
            'type_activite_id' => $this->typeActiviteId,
            'equipement_id'    => $this->equipementId,
            'date_seance'      => $this->dateSeance,
            'duree_minutes'    => $this->dureeMinutes,
            'notes'            => $this->notes,
        ];
    }
}
