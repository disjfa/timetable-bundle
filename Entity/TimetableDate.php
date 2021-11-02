<?php

namespace Disjfa\TimetableBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\Table(name="timetable_date")
 */
class TimetableDate
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
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
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $endDate;

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

    public function getStartDate(): DateTime
    {
        if (null !== $this->startDate) {
            return $this->startDate;
        }

        $startDate = null;
        if (0 === $this->items->count()) {
            $startDate = $this->dateAt;
        }

        foreach ($this->items as $item) {
            if (null === $startDate) {
                $startDate = $item->getDateStart();
            }

            if ($item->getDateStart() < $startDate) {
                $startDate = $item->getDateStart();
            }
        }

        $this->startDate = $startDate;

        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        if (null !== $this->endDate) {
            return $this->endDate;
        }

        $endDate = null;
        if (0 === $this->items->count()) {
            $endDate = $this->dateAt;
        }

        foreach ($this->items as $item) {
            if (null === $endDate) {
                $endDate = $item->getDateStart();
            }

            if ($item->getDateEnd() > $endDate) {
                $endDate = $item->getDateEnd();
            }
        }

        $this->endDate = $endDate;

        return $this->endDate;
    }

    public function getHeaders()
    {
        $startDate = clone $this->getStartDate();
        $endDate = clone $this->getEndDate();

        $minutes = 60 * 15;
        $timeStart = $startDate->getTimestamp();

        $headers = [];
        while ($startDate < $endDate) {
            $start = ($startDate->getTimestamp() - $timeStart) / $minutes + 2;
            $headers[] = [
                'date' => clone $startDate,
                'start' => $start,
                'end' => $start + 4,
            ];

            $startDate->modify('+1 hour');
        }

        return $headers;
    }
}
