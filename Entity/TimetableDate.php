<?php

namespace Disjfa\TimetableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Table(name: 'timetable_date')]
#[ORM\Entity]
class TimetableDate
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

    /**
     * @var Timetable
     */
    #[ORM\ManyToOne(targetEntity: Timetable::class, inversedBy: 'dates')]
    private $timetable;

    /**
     * @var TimetableItem[]|ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: TimetableItem::class, mappedBy: 'date')]
    #[ORM\OrderBy(['dateStart' => 'ASC'])]
    private $items;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date_at', type: 'date')]
    private $dateAt;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var ArrayCollection
     */
    private $headers;

    public function __construct(Timetable $timetable)
    {
        $this->timetable = $timetable;
        $this->dateAt = new \DateTime();
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

    public function getTimetable(): Timetable
    {
        return $this->timetable;
    }

    public function setTimetable(Timetable $timetable): void
    {
        $this->timetable = $timetable;
    }

    public function getDateAt(): \DateTime
    {
        return $this->dateAt;
    }

    public function setDateAt(\DateTime $dateAt): void
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

    public function getStartDate(): \DateTime
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

    public function getEndDate(): \DateTime
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

        // strep in minutes, must be devisible by 60, for example 5, 10, 15
        $step = 15;
        // minutes per step, 15 * 60 = 900
        $minutes = 60 * $step;
        // 60 / 15 = grid size 4
        $size = 60 / $step;
        // side, vertical or horizontal
        $side = $this->getTimetable()->getSide();
        $placesCount = $this->getTimetable()->getPlaces()->count() + 2;

        $timeStart = $startDate->getTimestamp();

        $headers = [];
        while ($startDate < $endDate) {
            $start = ($startDate->getTimestamp() - $timeStart) / $minutes + 2;
            $end = $start + 4;

            if ('vertical' === $side) {
                $class = "grid-column: $start / $end; grid-row: 1;";
                $line = "grid-column: $start / $end; grid-row: 1 / $placesCount;";
            } else {
                $class = "grid-row: $start / $end; grid-column: 1;";
                $line = "grid-row: $start / $end; grid-column: 1 / $placesCount;";
            }

            $headers[] = [
                'date' => clone $startDate,
                'start' => $start,
                'end' => $start + 4,
                'class' => $class,
                'line' => $line,
            ];

            $startDate->modify('+1 hour');
        }

        return $headers;
    }
}
