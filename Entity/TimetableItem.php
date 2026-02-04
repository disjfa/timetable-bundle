<?php

namespace Disjfa\TimetableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Table(name: 'timetable_item')]
#[ORM\Entity]
class TimetableItem
{
    /**
     * @var string
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'title', type: 'string')]
    private $title;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private $description;

    /**
     * @var TimetablePlace
     */
    #[ORM\ManyToOne(targetEntity: TimetablePlace::class, inversedBy: 'items')]
    private $place;

    /**
     * @var TimetableDate
     */
    #[ORM\ManyToOne(targetEntity: TimetableDate::class, inversedBy: 'items')]
    private $date;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date_start', type: 'datetime')]
    private $dateStart;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date_end', type: 'datetime')]
    private $dateEnd;
    /**
     * @var int
     */
    private $start;
    /**
     * @var int
     */
    private $end;

    public function __construct(TimetablePlace $place, TimetableDate $date)
    {
        $this->place = $place;
        $this->date = $date;
        $this->dateStart = $date->getDateAt();
        $this->dateEnd = $date->getDateAt();
    }

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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getPlace(): TimetablePlace
    {
        return $this->place;
    }

    public function setPlace(TimetablePlace $place): void
    {
        $this->place = $place;
    }

    public function getDate(): TimetableDate
    {
        return $this->date;
    }

    public function setDate(TimetableDate $date): void
    {
        $this->date = $date;
    }

    public function getDateStart(): \DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTime $dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    public function getDateEnd(): \DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTime $dateEnd): void
    {
        $this->dateEnd = $dateEnd;
    }

    public function getStart(): int
    {
        if (null !== $this->start) {
            return $this->start;
        }

        $minutes = 60 * 15;
        $startDate = $this->getDate()->getStartDate();
        $this->start = floor(($this->dateStart->getTimestamp() - $startDate->getTimestamp()) / $minutes + 2);

        return $this->start;
    }

    public function getEnd(): int
    {
        if (null !== $this->end) {
            return $this->end;
        }

        $minutes = 60 * 15;
        $startDate = $this->getDate()->getStartDate();
        $this->end = floor(($this->dateEnd->getTimestamp() - $startDate->getTimestamp()) / $minutes + 2);

        return $this->end;
    }
}
