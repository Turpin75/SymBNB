<?php

namespace App\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("title", message="Une annonce posséde déjà ce titre, merci de le modifier")
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner ce champ.")
     * @Assert\Length(max=255, maxMessage="Le titre ne peut dépasser {{limit}} caractères !")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ.")
     * @Assert\LessThan(value=1000000000, message = "Le prix ne peut dépasser {{ compared_value }}")
     * @Assert\GreaterThan(value=0, message = "Le prix doit être supérieur à {{ compared_value }}") 
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ.")
     * @Assert\Length(max=8000, maxMessage="L'introduction ne peut dépasser {{limit}} caractères !")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ.")
     * @Assert\Length(max=8000, maxMessage="Le contenu ne peut dépasser {{limit}} caractères !")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner ce champ.")
     * @Assert\Url(message="Veuillez saisir une url valide !")
     * @Assert\Length(max=255, maxMessage="L'url de l'image ne peut dépasser {{limit}} caractères !")
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ.")
     * @Assert\LessThan(value=1000, message = "Le nombre de chambres ne peut dépasser {{ compared_value }}")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="ad", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Permet d'initilaliser le slug
     * Ici on définit le slug uniquement pour les nouvelles annonces
     * Les anciennes annonces, même mises à jour, garderont leur ancien slug,
     * pour garder un bon référencement(sur goolge par exemple)
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     */
    public function initializeSlug()
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }
    }

    /**
     * Permet de retourner le commentaire d'un auteur
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author)
    {
        foreach($this->comments as $comment)
        {
            if($comment->getAuthor() === $author)
            {
                return $comment;
            }
            else
            {
                null;
            }
        }
    }
    /**
     * Calcul la moyenne des notes d'une annonce
     *
     * @return moyenne
     */
    public function getAvgRatings()
    {
        // Calculer le somme des notes
        $sum = array_reduce($this->comments->toArray(), function($total, $comment)
        {
            $total += $comment->getRating();
            return $total;
        }, 0);

        // Faire la division pour avoir la moyenne
        if(count($this->comments) > 0)
        {
            return $sum / count($this->comments);
        }
        else
        {
            return 0;
        }
    }

    /**
     * Retourne un tableau d'objets Datetime représentant les jours indisponibles
     */
    public function getUnavailableDays()
    {
        $unavailableDays = [];

        foreach($this->bookings as $booking)
        {
            // Calculer les jours qui se trouvent entre la date d'arrivée et de celle de départ
            $resultat = range
            (
                $booking->getStartDate()->getTimestamp(),
                $booking->getEndDate()->getTimestamp(),
                24*60*60
            );

            $days = array_map(function($dayTimestamp)
            {
                return new \DateTime(date('Y-m-d', $dayTimestamp));
            }, $resultat);

            $unavailableDays = array_merge($unavailableDays, $days);
        }

        return $unavailableDays;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) 
        {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) 
        {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) 
            {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }
}
