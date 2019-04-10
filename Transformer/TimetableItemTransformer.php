<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\TimetableItem;
use Disjfa\TimetableBundle\Timetable\DatesMutator;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TimetableItemTransformer extends TransformerAbstract
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
     * TimetableItemTransformer constructor.
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
     * @param TimetableItem $timetableItem
     *
     * @return array
     */
    public function transform(TimetableItem $timetableItem)
    {
        $datePresenter = $this->datesMutator->getDatePresenter($timetableItem->getDate());

        $minutes = 60 * 15;
        $timeStart = $datePresenter->getDateStart()->getTimestamp();

        $data = [
            'id' => $timetableItem->getId(),
            'title' => $timetableItem->getTitle(),
            'dateStart' => $timetableItem->getDateStart(),
            'dateEnd' => $timetableItem->getDateEnd(),
            'place' => $timetableItem->getPlace()->getId(),
            'date' => $timetableItem->getDate()->getId(),
            'start' => floor(($timetableItem->getDateStart()->getTimestamp() - $timeStart) / $minutes + 2),
            'end' => floor(($timetableItem->getDateEnd()->getTimestamp() - $timeStart) / $minutes + 2),
        ];

//        if ($this->authorizationChecker->isGranted(UserVoter::SEE_EMAIL, $user)) {
//            $data['email'] = $user->email();
//        }
        return $data;
    }
}
