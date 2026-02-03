<?php

namespace Disjfa\TimetableBundle\Security;

use Disjfa\TimetableBundle\Entity\Timetable;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TimetableVoter extends Voter
{
    public const VIEW = 'view';
    public const CREATE = 'create';
    public const UPDATE = 'update';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($attribute, [self::VIEW, self::UPDATE]) && $subject instanceof Timetable) {
            return true;
        }

        if (in_array($attribute, [self::CREATE]) && Timetable::class === $subject) {
            return true;
        }

        return false;
    }

    /**
     * @param Timetable $timetable
     */
    protected function voteOnAttribute(string $attribute, mixed $timetable, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (false === $user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($timetable, $user);
            case self::CREATE:
                return $this->canCreate($timetable, $user);
            case self::UPDATE:
                return $this->canUpdate($timetable, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @return bool
     */
    private function canView(Timetable $timetable, UserInterface $user)
    {
        return true;
    }

    /**
     * @return bool
     */
    private function canUpdate(Timetable $timetable, UserInterface $user)
    {
        if ($timetable->getUsers()->contains($user)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canCreate(Timetable $timetable, UserInterface $user)
    {
        return false;
    }
}
