<?php

namespace App\Entity;

use App\Repository\TricksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=TricksRepository::class)
 * @UniqueEntity(fields={"name"}, message="Il existe déjà un Trick avec ce Nom")
 */
class Tricks
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Groups::class, inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_trick;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $video_urls = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     */
    private $updated_by;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $created_by;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="trick", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=TricksPictures::class, mappedBy="tricks", orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

    /**
     * @Assert\All({
     *     @Assert\Image(mimeTypes="image/jpeg")
 *     })
     */
    private $pictureFiles;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->pictures = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGroupTrick(): ?Groups
    {
        return $this->group_trick;
    }

    public function setGroupTrick(?Groups $group_trick): self
    {
        $this->group_trick = $group_trick;

        return $this;
    }

    public function getVideoUrls(): ?string
    {

        return implode(",", $this->video_urls);
    }

    public function setVideoUrls(?string $video_urls): self
    {
        $entry = [];
        $urls = explode(",", $video_urls);

        foreach($urls as $url) {
            if(filter_var($url, FILTER_VALIDATE_URL)) {
                $entry[] = $url;
            }
        }

        $this->video_urls = $entry;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedBy(): ?Users
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?Users $updated_by): self
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getCreatedBy(): ?Users
    {
        return $this->created_by;
    }

    public function setCreatedBy(?Users $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TricksPictures[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(TricksPictures $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setTricks($this);
        }

        return $this;
    }

    public function removePicture(TricksPictures $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getTricks() === $this) {
                $picture->setTricks(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPictureFiles()
    {
        return $this->pictureFiles;
    }

    /**
     * @param mixed $pictureFiles
     * @return Tricks
     */
    public function setPictureFiles($pictureFiles)
    {
        foreach ($pictureFiles as $pictureFile) {
            $picture = (new TricksPictures())->setImageFile($pictureFile);
            $this->addPicture($picture);
        }
        $this->pictureFiles = $pictureFiles;
        return $this;
    }
}
