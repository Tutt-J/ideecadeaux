<?php

namespace App\Entity;

use App\Repository\GiftRepository;
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
     * @ORM\Column(type="text")
     */
    private $details;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gifts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ask_by;

    /**
     * @ORM\Column(type="boolean")
     */
    private $already_buy;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

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

    public function getAskBy(): ?User
    {
        return $this->ask_by;
    }

    public function setAskBy(?User $ask_by): self
    {
        $this->ask_by = $ask_by;

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
}
