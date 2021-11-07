<?php

namespace App\Entity;

use App\Repository\ChildRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChildRepository::class)
 */
class Child
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
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="children")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent;


    /**
     * @ORM\OneToMany(targetEntity=GiftGroup::class, mappedBy="child", orphanRemoval=true)
     */
    private $giftGroups;

    /**
     * @ORM\OneToMany(targetEntity=Gift::class, mappedBy="child", orphanRemoval=true)
     */
    private $gifts;

    public function __construct()
    {
        $this->giftGroups = new ArrayCollection();
        $this->gifts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getParent(): ?User
    {
        return $this->parent;
    }

    public function setParent(?User $parent): self
    {
        $this->parent = $parent;

        return $this;
    }


    /**
     * @return Collection|GiftGroup[]
     */
    public function getGiftGroups(): Collection
    {
        return $this->giftGroups;
    }

    public function addGiftGroup(GiftGroup $giftGroup): self
    {
        if (!$this->giftGroups->contains($giftGroup)) {
            $this->giftGroups[] = $giftGroup;
            $giftGroup->setChild($this);
        }

        return $this;
    }

    public function removeGiftGroup(GiftGroup $giftGroup): self
    {
        if ($this->giftGroups->removeElement($giftGroup)) {
            // set the owning side to null (unless already changed)
            if ($giftGroup->getChild() === $this) {
                $giftGroup->setChild(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Gift[]
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function addGift(Gift $gift): self
    {
        if (!$this->gifts->contains($gift)) {
            $this->gifts[] = $gift;
            $gift->setChild($this);
        }

        return $this;
    }

    public function removeGift(Gift $gift): self
    {
        if ($this->gifts->removeElement($gift)) {
            // set the owning side to null (unless already changed)
            if ($gift->getChild() === $this) {
                $gift->setChild(null);
            }
        }

        return $this;
    }
}
