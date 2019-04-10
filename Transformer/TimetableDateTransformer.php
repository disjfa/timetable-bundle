<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Timetable\DatesMutator;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TimetableDateTransformer extends TransformerAbstract
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationCheker;
    /**
     * @var DatesMutator
     */
    private $datesMutator;

    /**
     * TimetableDateTransformer constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationCheker
     * @param DatesMutator                  $datesMutator
     */
    public function __construct(AuthorizationCheckerInterface $authorizationCheker, DatesMutator $datesMutator)
    {
        $this->authorizationCheker = $authorizationCheker;
        $this->datesMutator = $datesMutator;
    }

    /**
     * @param TimetableDate $timetableDate
     *
     * @return array
     */
    public function transform(TimetableDate $timetableDate)
    {
        $datePresenter = $this->datesMutator->getDatePresenter($timetableDate);

        $data = [
            'id' => $timetableDate->getId(),
            'title' => $timetableDate->getTitle(),
            'dateAt' => $timetableDate->getDateAt(),
            'headers' => $datePresenter->getHeaders(),
        ];

        return $data;
    }
}
