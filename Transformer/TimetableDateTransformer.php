<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\TimetableDate;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TimetableDateTransformer extends TransformerAbstract
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationCheker;

    /**
     * TimetableDateTransformer constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationCheker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationCheker)
    {
        $this->authorizationCheker = $authorizationCheker;
    }

    /**
     * @param TimetableDate $timetableDate
     *
     * @return array
     */
    public function transform(TimetableDate $timetableDate)
    {
        return [
            'id' => $timetableDate->getId(),
            'title' => $timetableDate->getTitle(),
            'dateAt' => $timetableDate->getDateAt(),
            'headers' => $timetableDate->getHeaders(),
        ];
    }
}
