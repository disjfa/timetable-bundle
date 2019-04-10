<?php

namespace Disjfa\TimetableBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="timetable_date")
 */
class TimetableDate
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var Timetable
     * @ORM\ManyToOne(targetEntity="Disjfa\TimetableBundle\Entity\Timetable", inversedBy="dates")
     */
    private $timetable;

    /**
     * @var TimetableItem[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Disjfa\TimetableBundle\Entity\TimetableItem", mappedBy="date")
     */
    private $items;

    /**
     * @var DateTime
     * @ORM\Column(name="date_at", type="date")
     */
    private $dateAt;

    /**
     * @var ArrayCollection
     */
    private $headers;

    public function __construct(Timetable $timetable)
    {
        $this->timetable = $timetable;
        $this->dateAt = new DateTime();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return Timetable
     */
    public function getTimetable(): Timetable
    {
        return $this->timetable;
    }

    /**
     * @param Timetable $timetable
     */
    public function setTimetable(Timetable $timetable): void
    {
        $this->timetable = $timetable;
    }

    /**
     * @return DateTime
     */
    public function getDateAt(): DateTime
    {
        return $this->dateAt;
    }

    /**
     * @param DateTime $dateAt
     */
    public function setDateAt(DateTime $dateAt): void
    {
        $this->dateAt = $dateAt;
    }

    /**
     * @return TimetableItem[]|ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }
}
