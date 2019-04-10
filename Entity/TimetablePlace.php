<?php

namespace Disjfa\TimetableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="timetable_place")
 */
class TimetablePlace
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
     * @ORM\ManyToOne(targetEntity="Disjfa\TimetableBundle\Entity\Timetable", inversedBy="places")
     */
    private $timetable;

    /**
     * @var int
     * @ORM\Column(name="seqnr", type="integer")
     */
    private $seqnr;

    /**
     * @var TimetableItem[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Disjfa\TimetableBundle\Entity\TimetableItem", mappedBy="place")
     */
    private $items;

    public function __construct(Timetable $timetable)
    {
        $this->timetable = $timetable;
        $this->seqnr = 0;
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
     * @return int
     */
    public function getSeqnr(): int
    {
        return $this->seqnr;
    }

    /**
     * @param int $seqnr
     */
    public function setSeqnr(int $seqnr): void
    {
        $this->seqnr = $seqnr;
    }

    /**
     * @return TimetableItem[]|ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }
}
