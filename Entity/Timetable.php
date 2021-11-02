<?php

namespace Disjfa\TimetableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\Table(name="timetable")
 */
class Timetable
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

    /**
     * @var string|null
     * @ORM\Column(name="body_bg", type="string", nullable=true)
     */
    private $bodyBg;

    /**
     * @var string|null
     * @ORM\Column(name="header_bg", type="string", nullable=true)
     */
    private $headerBg;

    /**
     * @var string|null
     * @ORM\Column(name="box_bg", type="string", nullable=true)
     */
    private $boxBg;

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

    /**
     * @return string|null
     */
    public function getBodyBg(): ?string
    {
        return $this->bodyBg;
    }

    /**
     * @param string|null $bodyBg
     */
    public function setBodyBg(?string $bodyBg): void
    {
        $this->bodyBg = $bodyBg;
    }

    /**
     * @return string|null
     */
    public function getHeaderBg(): ?string
    {
        return $this->headerBg;
    }

    /**
     * @param string|null $headerBg
     */
    public function setHeaderBg(?string $headerBg): void
    {
        $this->headerBg = $headerBg;
    }

    /**
     * @return string|null
     */
    public function getBoxBg(): ?string
    {
        return $this->boxBg;
    }

    /**
     * @param string|null $boxBg
     */
    public function setBoxBg(?string $boxBg): void
    {
        $this->boxBg = $boxBg;
    }
}
