<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\TimetableDate;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TimetableDatesTransformer extends TransformerAbstract
{
    private $authorizationCheker;

    public function __construct(AuthorizationCheckerInterface $authorizationCheker)
    {
        $this->authorizationCheker = $authorizationCheker;
    }

    public function transform(TimetableDate $timetableDate)
    {
        $data = [
            'id' => $timetableDate->getId(),
            'title' => $timetableDate->getTitle(),
            'dateAt' => $timetableDate->getDateAt(),
        ];

//        if ($this->authorizationChecker->isGranted(UserVoter::SEE_EMAIL, $user)) {
//            $data['email'] = $user->email();
//        }
        return $data;
    }
}
