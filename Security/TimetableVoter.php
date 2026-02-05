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

        if (self::VIEW === $attribute) {
            return $this->canView($timetable, $user);
        }

        if (false === $user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if (self::CREATE === $attribute) {
            return $this->canCreate($user);
        }

        if (self::UPDATE === $attribute) {
            return $this->canUpdate($timetable, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Timetable $timetable, ?UserInterface $user): bool
    {
        return true;
    }

    private function canUpdate(Timetable $timetable, UserInterface $user): bool
    {
        if ($timetable->getUsers()->contains($user)) {
            return true;
        }

        return false;
    }

    private function canCreate(UserInterface $user): bool
    {
        if ($user instanceof UserInterface) {
            return true;
        }

        return false;
    }
}
