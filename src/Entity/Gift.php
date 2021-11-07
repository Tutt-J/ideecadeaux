<?php

namespace App\Entity;

use App\Repository\GiftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GiftRepository::class)
 */
class Gift
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;


    /**
     * @ORM\Column(type="boolean")
     */
    private $already_buy;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity=GiftGroup::class, inversedBy="gifts")
     */
    private $giftGroup;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gifts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Child::class, inversedBy="gifts")
     */
    private $child;

    public function __construct()
    {
        $this->giftGroup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getAlreadyBuy(): ?bool
    {
        return $this->already_buy;
    }

    public function setAlreadyBuy(bool $already_buy): self
    {
        $this->already_buy = $already_buy;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|GiftGroup[]
     */
    public function getGiftGroup(): Collection
    {
        return $this->giftGroup;
    }

    public function addGiftGroup(GiftGroup $giftGroup): self
    {
        if (!$this->giftGroup->contains($giftGroup)) {
            $this->giftGroup[] = $giftGroup;
        }

        return $this;
    }

    public function removeGiftGroup(GiftGroup $giftGroup): self
    {
        $this->giftGroup->removeElement($giftGroup);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getChild(): ?Child
    {
        return $this->child;
    }

    public function setChild(?Child $child): self
    {
        $this->child = $child;

        return $this;
    }
}
