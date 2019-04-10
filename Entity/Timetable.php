<?php

namespace Disjfa\TimetableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="timetable")
 */
class Timetable
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
     * @var string
     * @ORM\Column(name="side", type="string")
     */
    private $side;

    /**
     * @var TimetablePlace[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Disjfa\TimetableBundle\Entity\TimetablePlace", mappedBy="timetable")
     * @ORM\OrderBy({"seqnr" = "ASC"})
     */
    private $places;

    /**
     * @var TimetableDate[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Disjfa\TimetableBundle\Entity\TimetableDate", mappedBy="timetable")
     * @ORM\OrderBy({"dateAt" = "ASC"})
     */
    private $dates;

    public function __construct()
    {
        $this->side = 'horizontal';
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
     * @return TimetablePlace[]|ArrayCollection
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @return TimetableDate[]|ArrayCollection
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * @return string
     */
    public function getSide(): string
    {
        return $this->side;
    }

    /**
     * @param string $side
     */
    public function setSide(string $side): void
    {
        $this->side = $side;
    }
}
