<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickActionVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['TRICK_EDIT', 'TRICK_DELETE'])
            && $subject instanceof \App\Entity\Trick;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // For each case, return true if subjet author is the user
        switch ($attribute) {
            case 'TRICK_EDIT':
                return $subject->getAuthor()->getId() == $user->getId();
                break;
            case 'TRICK_DELETE':
                return $subject->getAuthor()->getId() == $user->getId();
                break;
        }

        return false;
    }
}
