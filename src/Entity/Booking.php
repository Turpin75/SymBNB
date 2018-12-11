<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="La date d'arrivée doit être au bon format !")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être supérieure à celle d'aujourd'hui !", groups={"back"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="La date de départ doit être au bon format !")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être supérieure à celle d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(max=8000, maxMessage="Le commentaire ne peut dépasser {{limit}} caractères !")
     */
    private $comments;

    /**
     * Callback appelé à chaque fois qu'on créé une réservation
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist()
    {
        if(empty($this->createdAt))
        {
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amount))
        {
            // Prix de l'annonce * le nombre de jours
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    /**
     * Nombre de jours entre la date d'arrivée et celle de départ
     *
     */
    public function getDuration()
    {
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    /**
     * Permet de connaître les dates disponibles
     */
    public function isBookableDates()
    {
        // Les dates indisponibles pour l'annonce
        $unavailableDays = $this->ad->getUnavailableDays();
        // Comparer les dates choisies avec les dates indisponibles
        $bookingDays = $this->getDays();

        // Tableau de chaines de caractères de mes journées
        $days = array_map(function($day)
        {
            return $day->format('Y-m-d');
        }, $bookingDays);

        $unavailableDay = array_map(function($day)
        {
            return $day->format('Y-m-d');
        }, $unavailableDays);

        foreach($days as $day)
        {
            if(array_search($day, $unavailableDay) !== false)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    /**
     * Permet de récupèrer le tableau  d'objets DateTime des jours de ma réservation
     */
    public function getDays()
    {
        $resultat = range
        (
            $this->getStartDate()->getTimestamp(),
            $this->getEndDate()->getTimestamp(),
            24*60*60
        );

        $days = array_map(function($dayTimestamp)
        {
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $resultat);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }
}
