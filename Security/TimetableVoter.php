<?php

namespace Disjfa\TimetableBundle\Security;

use Disjfa\TimetableBundle\Entity\Timetable;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserInterface;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TimetableVoter extends Voter
{
    const VIEW = 'view';
    const PATCH = 'patch';
    const POST = 'post';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if ( ! in_array($attribute, [self::VIEW, self::PATCH, self::POST])) {
            return false;
        }

        if ( ! $subject instanceof Timetable) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param Timetable      $timetable
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $timetable, TokenInterface $token)
    {
        $user = $token->getUser();

        if (false === $user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($timetable, $user);
            case self::POST:
                return $this->canPost($timetable, $user);
            case self::PATCH:
                return $this->canPatch($timetable, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    /**
     * @param Timetable $timetable
     * @param User      $user
     *
     * @return bool
     */
    private function canView(Timetable $timetable, User $user)
    {
        return false;
    }

    /**
     * @param Timetable $timetable
     * @param User      $user
     *
     * @return bool
     */
    private function canPatch(Timetable $timetable, User $user)
    {
        return false;
    }

    /**
     * @param Timetable $timetable
     * @param User      $user
     *
     * @return bool
     */
    private function canPost(Timetable $timetable, User $user)
    {
        return false;
    }
}
