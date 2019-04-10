<?php

namespace Disjfa\TimetableBundle\Timetable;

use Disjfa\TimetableBundle\Entity\TimetableDate;
use Doctrine\Common\Collections\ArrayCollection;

class DatesMutator
{
    private $dates;

    public function __construct()
    {
        $this->dates = new ArrayCollection();
    }

    public function getDatePresenter(TimetableDate $timetableDate)
    {
        $datePresenter = $this->dates->get($timetableDate->getId());

        if (null === $datePresenter) {
            $datePresenter = new DatePresenter($timetableDate);
            $this->dates->set($timetableDate->getId(), $datePresenter);
        }

        return $datePresenter;
    }
}
