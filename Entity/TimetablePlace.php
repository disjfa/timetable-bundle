<?php

namespace Disjfa\TimetableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Table(name: 'timetable_place')]
#[ORM\Entity]
class TimetablePlace
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
    #[ORM\ManyToOne(targetEntity: Timetable::class, inversedBy: 'places')]
    private $timetable;

    /**
     * @var int
     */
    #[ORM\Column(name: 'seqnr', type: 'integer')]
    private $seqnr;

    /**
     * @var TimetableItem[]|ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: TimetableItem::class, mappedBy: 'place')]
    private $items;

    private ?int $index = null;

    public function __construct()
    {
        $this->seqnr = 0;
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

    public function getSeqnr(): int
    {
        return $this->seqnr;
    }

    public function setSeqnr(int $seqnr): void
    {
        $this->seqnr = $seqnr;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function setIndex(?int $index): void
    {
        $this->index = $index;
    }

    /**
     * @return TimetableItem[]|ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    public function getClass()
    {
        $side = $this->getTimetable()->getSide();
        $index = $this->getIndex();

        if ('vertical' === $side) {
            return "grid-column: 1; grid-row: $index;";
        }

        return "grid-row: 1; grid-column: $index;";
    }
}
