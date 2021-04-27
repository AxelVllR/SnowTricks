<?php

namespace App\Entity;

use App\Repository\TricksPicturesRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=TricksPicturesRepository::class)
 *
 */
class TricksPictures
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
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity=Tricks::class, inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tricks;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_primary;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getTricks(): ?Tricks
    {
        return $this->tricks;
    }

    public function setTricks(?Tricks $tricks): self
    {
        $this->tricks = $tricks;

        return $this;
    }

    public function getIsPrimary(): ?bool
    {
        return $this->is_primary;
    }

    public function setIsPrimary(?bool $is_primary): self
    {
        $this->is_primary = $is_primary;

        return $this;
    }
}
