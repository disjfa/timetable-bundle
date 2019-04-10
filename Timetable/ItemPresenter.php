<?php

namespace Disjfa\TimetableBundle\Timetable;

use Disjfa\TimetableBundle\Entity\TimetableItem;
use JsonSerializable;

class ItemPresenter implements JsonSerializable
{
    /**
     * @var TimetableItem
     */
    private $item;
    /**
     * @var int
     */
    private $start;
    /**
     * @var int
     */
    private $end;

    /**
     * ItemPresenter constructor.
     *
     * @param TimetableItem $item
     * @param DatePresenter $datePresenter
     */
    public function __construct($item, $datePresenter)
    {
        $this->item = $item;
        $minutes = 60 * 15;
        $timeStart = $datePresenter->getDateStart()->getTimestamp();
        $this->start = floor(($item->getDateStart()->getTimestamp() - $timeStart) / $minutes + 2);
        $this->end = floor(($item->getDateEnd()->getTimestamp() - $timeStart) / $minutes + 2);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->item->getId(),
            'title' => $this->item->getTitle(),
            'place' => $this->item->getPlace()->getId(),
            'date' => $this->item->getDate()->getId(),
            'start' => $this->start,
            'end' => $this->end,
            'dateStart' => $this->item->getDateStart(),
            'dateEnd' => $this->item->getDateEnd(),
        ];
    }

    /**
     * @return TimetableItem
     */
    public function getItem(): TimetableItem
    {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }
}
