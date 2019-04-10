<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\TimetablePlace;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TimetablePlacesTransformer extends TransformerAbstract
{
    private $authorizationCheker;

    public function __construct(AuthorizationCheckerInterface $authorizationCheker)
    {
        $this->authorizationCheker = $authorizationCheker;
    }

    public function transform(TimetablePlace $timetablePlace)
    {
        $data = [
            'id' => $timetablePlace->getId(),
            'title' => $timetablePlace->getTitle(),
        ];

//        if ($this->authorizationChecker->isGranted(UserVoter::SEE_EMAIL, $user)) {
//            $data['email'] = $user->email();
//        }
        return $data;
    }
}
