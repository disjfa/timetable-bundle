<?php

namespace Disjfa\TimetableBundle\Timetable;

use Disjfa\TimetableBundle\Entity\TimetablePlace;
use JsonSerializable;

class PlacePresenter implements JsonSerializable
{
    /**
     * @var TimetablePlace
     */
    private $timetablePlace;

    /**
     * PlacePresenter constructor.
     *
     * @param TimetablePlace $place
     */
    public function __construct(TimetablePlace $timetablePlace)
    {
        $this->timetablePlace = $timetablePlace;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->timetablePlace->getId(),
            'title' => $this->timetablePlace->getTitle(),
        ];
    }

    /**
     * @return TimetablePlace
     */
    public function getTimetablePlace(): TimetablePlace
    {
        return $this->timetablePlace;
    }
}
