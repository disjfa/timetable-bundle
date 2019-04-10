<?php

namespace Disjfa\TimetableBundle\Timetable;

use Disjfa\TimetableBundle\Entity\Timetable;
use JsonSerializable;

class TimetablePresenter implements JsonSerializable
{
    /**
     * @var Timetable
     */
    private $timetable;
    /**
     * @var DatePresenter[]
     */
    private $dates;
    /**
     * @var ItemPresenter[]
     */
    private $items;
    /**
     * @var PlacePresenter[]
     */
    private $places;

    /**
     * @param Timetable $timetable
     * @param bool      $deep
     */
    public function __construct(Timetable $timetable, bool $deep = true)
    {
        $this->timetable = $timetable;
        $this->dates = [];
        $this->items = [];
        $this->places = [];

        if (false === $deep) {
            return;
        }

        foreach ($timetable->getDates() as $date) {
            $datePresenter = new DatePresenter($date);
            $this->dates[] = $datePresenter;

            foreach ($date->getItems() as $item) {
                $this->items[] = new ItemPresenter($item, $datePresenter);
            }
        }

        foreach ($timetable->getPlaces() as $place) {
            $this->places[] = new PlacePresenter($place);
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $dates = [];
        foreach ($this->dates as $date) {
            $dates[] = $date->jsonSerialize();
        }

        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->jsonSerialize();
        }
        $places = [];
        foreach ($this->places as $place) {
            $places[] = $place->jsonSerialize();
        }

        return [
            'id' => $this->timetable->getId(),
            'title' => $this->timetable->getTitle(),
            'side' => $this->timetable->getSide(),
            'dates' => $dates,
            'items' => $items,
            'places' => $places,
        ];
    }

    /**
     * @return Timetable
     */
    public function getTimetable(): Timetable
    {
        return $this->timetable;
    }

    /**
     * @return DatePresenter[]
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    /**
     * @return ItemPresenter[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return PlacePresenter[]
     */
    public function getPlaces(): array
    {
        return $this->places;
    }
}
