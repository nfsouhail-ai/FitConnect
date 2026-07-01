<?php
// ============================================================
// app/Entities/Adherent.php
// Entité métier : Adhérent
// ============================================================

declare(strict_types=1);

namespace App\Entities;

class Adherent
{
    // Attributs privés — accès uniquement via les accesseurs
    private ?int    $id;
    private string  $nom;
    private string  $prenom;
    private string  $email;
    private ?string $telephone;
    private string  $dateNaissance;
    private int     $salleId;
    private ?string $createdAt;

    public function __construct(
        string  $nom,
        string  $prenom,
        string  $email,
        string  $dateNaissance,
        int     $salleId,
        ?string $telephone = null,
        ?int    $id        = null,
        ?string $createdAt = null
    ) {
        $this->nom           = $nom;
        $this->prenom        = $prenom;
        $this->email         = $email;
        $this->dateNaissance = $dateNaissance;
        $this->salleId       = $salleId;
        $this->telephone     = $telephone;
        $this->id            = $id;
        $this->createdAt     = $createdAt;
    }

    // ── Accesseurs (getters) ─────────────────────────────────
    public function getId(): ?int       { return $this->id; }
    public function getNom(): string    { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string  { return $this->email; }
    public function getTelephone(): ?string   { return $this->telephone; }
    public function getDateNaissance(): string { return $this->dateNaissance; }
    public function getSalleId(): int   { return $this->salleId; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // ── Mutateurs (setters) ──────────────────────────────────
    public function setNom(string $nom): void            { $this->nom = $nom; }
    public function setPrenom(string $prenom): void      { $this->prenom = $prenom; }
    public function setEmail(string $email): void        { $this->email = $email; }
    public function setTelephone(?string $tel): void     { $this->telephone = $tel; }
    public function setDateNaissance(string $d): void    { $this->dateNaissance = $d; }
    public function setSalleId(int $id): void            { $this->salleId = $id; }

    /**
     * Nom complet formaté pour l'affichage
     */
    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . strtoupper($this->nom);
    }

    /**
     * Crée une instance Adherent depuis un tableau associatif (ex. : résultat PDO)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            nom:           $data['nom'],
            prenom:        $data['prenom'],
            email:         $data['email'],
            dateNaissance: $data['date_naissance'],
            salleId:       (int) $data['salle_id'],
            telephone:     $data['telephone'] ?? null,
            id:            isset($data['id']) ? (int) $data['id'] : null,
            createdAt:     $data['created_at'] ?? null
        );
    }

    /**
     * Convertit l'entité en tableau pour la persistance
     */
    public function toArray(): array
    {
        return [
            'nom'            => $this->nom,
            'prenom'         => $this->prenom,
            'email'          => $this->email,
            'telephone'      => $this->telephone,
            'date_naissance' => $this->dateNaissance,
            'salle_id'       => $this->salleId,
        ];
    }
}
