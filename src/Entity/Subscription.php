<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    private ?string $UMPMail = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $homeLaboratory = null;

    #[ORM\Column(length: 255)]
    private ?string $homeInstitution = null;

    #[ORM\Column(length: 255)]
    private ?string $searchTheme = null;

    #[ORM\Column(length: 255)]
    private ?string $inscriptionYear = null;

    /**
     * @var array<string>
     */
    #[ORM\Column(length: 255)]
    private ?array $ateliersDeFormation = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AnnonceFormation $annonceFormation = null;

    #[ORM\ManyToOne(inversedBy: 'userSubscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $subscriber = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getUMPMail(): ?string
    {
        return $this->UMPMail;
    }

    public function setUMPMail(string $UMPMail): self
    {
        $this->UMPMail = $UMPMail;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getHomeLaboratory(): ?string
    {
        return $this->homeLaboratory;
    }

    public function setHomeLaboratory(string $homeLaboratory): self
    {
        $this->homeLaboratory = $homeLaboratory;

        return $this;
    }

    public function getHomeInstitution(): ?string
    {
        return $this->homeInstitution;
    }

    public function setHomeInstitution(string $homeInstitution): self
    {
        $this->homeInstitution = $homeInstitution;

        return $this;
    }

    public function getSearchTheme(): ?string
    {
        return $this->searchTheme;
    }

    public function setSearchTheme(string $searchTheme): self
    {
        $this->searchTheme = $searchTheme;

        return $this;
    }

    public function getInscriptionYear(): ?string
    {
        return $this->inscriptionYear;
    }

    public function setInscriptionYear(string $inscriptionYear): self
    {
        $this->inscriptionYear = $inscriptionYear;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getAteliersDeFormation(): ?array
    {
        return $this->ateliersDeFormation;
    }

    /**
     * @param array<string> $ateliersDeFormation
     */
    public function setAteliersDeFormation(array $ateliersDeFormation): self
    {
        $this->ateliersDeFormation = $ateliersDeFormation;

        return $this;
    }

    public function getAnnonceFormation(): ?AnnonceFormation
    {
        return $this->annonceFormation;
    }

    public function setAnnonceFormation(?AnnonceFormation $annonceFormation): self
    {
        $this->annonceFormation = $annonceFormation;

        return $this;
    }

    public function getSubscriber(): ?User
    {
        return $this->subscriber;
    }

    public function setSubscriber(?User $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
