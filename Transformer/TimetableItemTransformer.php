<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\TimetableItem;
use League\Fractal\TransformerAbstract;

class TimetableItemTransformer extends TransformerAbstract
{
    public function transform(TimetableItem $timetableItem): array
    {
        return [
            'id' => $timetableItem->getId(),
            'title' => $timetableItem->getTitle(),
            'dateStart' => $timetableItem->getDateStart(),
            'dateEnd' => $timetableItem->getDateEnd(),
            'place' => $timetableItem->getPlace()->getId(),
            'date' => $timetableItem->getDate()->getId(),
            'start' => $timetableItem->getStart(),
            'end' => $timetableItem->getEnd(),
        ];
    }
}
