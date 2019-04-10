<?php

namespace Disjfa\TimetableBundle\Timetable;

use DateTime;
use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Entity\TimetableItem;
use JsonSerializable;

class DatePresenter implements JsonSerializable
{
    /**
     * @var TimetableDate
     */
    private $timetableDate;
    /**
     * @var DateTime
     */
    private $dateStart;
    /**
     * @var DateTime
     */
    private $dateEnd;
    /**
     * @var array
     */
    private $headers;

    /**
     * DatePresenter constructor.
     *
     * @param TimetableDate $timetableDate
     */
    public function __construct(TimetableDate $timetableDate)
    {
        $this->timetableDate = $timetableDate;
        $this->setupStartAndEndDate($timetableDate->getItems());
        $this->setupHeaders();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->timetableDate->getId(),
            'title' => $this->timetableDate->getTitle(),
            'dateAt' => $this->timetableDate->getDateAt(),
            'dateStart' => $this->dateStart,
            'dateEnd' => $this->dateEnd,
            'headers' => $this->headers,
        ];
    }

    /**
     * @param TimetableItem[] $items
     */
    private function setupStartAndEndDate($items)
    {
        foreach ($items as $item) {
            if (null === $this->dateStart) {
                $this->dateStart = clone $item->getDateStart();
            }
            if (null === $this->dateEnd) {
                $this->dateEnd = clone $item->getDateStart();
            }
            if ($this->dateStart > $item->getDateStart()) {
                $this->dateStart = clone $item->getDateStart();
            }
            if ($this->dateStart > $item->getDateEnd()) {
                $this->dateStart = clone $item->getDateEnd();
            }
            if ($this->dateEnd < $item->getDateStart()) {
                $this->dateEnd = clone $item->getDateStart();
            }
            if ($this->dateEnd < $item->getDateEnd()) {
                $this->dateEnd = clone $item->getDateEnd();
            }
        }

        $this->dateStart->setTime($this->dateStart->format('G'), 0);
        $this->dateEnd = clone $this->dateEnd;
        if (0 !== (int) $this->dateEnd->format('i')) {
            $this->dateEnd->setTime((int) $this->dateEnd->format('G') + 1, 0);
        }
    }

    private function setupHeaders()
    {
        $minutes = 60 * 15;
        $dateStart = clone $this->dateStart;
        $dateEnd = clone $this->dateEnd;
        $timeStart = $dateStart->getTimestamp();

        $this->headers = [];
        while ($dateStart < $dateEnd) {
            $start = ($dateStart->getTimestamp() - $timeStart) / $minutes + 2;
            $this->headers[] = [
                'date' => clone $dateStart,
                'start' => $start,
                'end' => $start + 4,
            ];
            $dateStart->modify('+1 hour');
        }
    }

    /**
     * @return DateTime
     */
    public function getDateStart(): DateTime
    {
        return $this->dateStart;
    }

    /**
     * @return DateTime
     */
    public function getDateEnd(): DateTime
    {
        return $this->dateEnd;
    }

    /**
     * @return TimetableDate
     */
    public function getTimetableDate(): TimetableDate
    {
        return $this->timetableDate;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
