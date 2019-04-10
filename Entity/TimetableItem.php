<?php

namespace Disjfa\TimetableBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="timetable_item")
 */
class TimetableItem
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
     * @var TimetablePlace
     * @ORM\ManyToOne(targetEntity="Disjfa\TimetableBundle\Entity\TimetablePlace", inversedBy="items")
     */
    private $place;

    /**
     * @var TimetableDate
     * @ORM\ManyToOne(targetEntity="Disjfa\TimetableBundle\Entity\TimetableDate", inversedBy="items")
     */
    private $date;

    /**
     * @var DateTime
     * @ORM\Column(name="date_start", type="datetime")
     */
    private $dateStart;

    /**
     * @var DateTime
     * @ORM\Column(name="date_end", type="datetime")
     */
    private $dateEnd;

    public function __construct(TimetablePlace $place, TimetableDate $date)
    {
        $this->place = $place;
        $this->date = $date;
        $this->dateStart = $date->getDateAt();
        $this->dateEnd = $date->getDateAt();
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
     * @return TimetablePlace
     */
    public function getPlace(): TimetablePlace
    {
        return $this->place;
    }

    /**
     * @param TimetablePlace $place
     */
    public function setPlace(TimetablePlace $place): void
    {
        $this->place = $place;
    }

    /**
     * @return TimetableDate
     */
    public function getDate(): TimetableDate
    {
        return $this->date;
    }

    /**
     * @param TimetableDate $date
     */
    public function setDate(TimetableDate $date): void
    {
        $this->date = $date;
    }

    /**
     * @return DateTime
     */
    public function getDateStart(): DateTime
    {
        return $this->dateStart;
    }

    /**
     * @param DateTime $dateStart
     */
    public function setDateStart(DateTime $dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return DateTime
     */
    public function getDateEnd(): DateTime
    {
        return $this->dateEnd;
    }

    /**
     * @param DateTime $dateEnd
     */
    public function setDateEnd(DateTime $dateEnd): void
    {
        $this->dateEnd = $dateEnd;
    }
}
