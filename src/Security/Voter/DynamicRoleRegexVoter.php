<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DynamicRoleRegexVoter extends Voter
{
    // On accepte tout ce qui commence par "REGEX:"
    protected function supports(string $attribute, mixed $subject): bool
    {
        return str_starts_with($attribute, 'REGEX:');
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Extraire la regex depuis l’attribut
        $regex = substr($attribute, strlen('REGEX:'));

        foreach ($user->getRoles() as $role) {
            if (preg_match('/' . $regex . '/', $role)) {
                return true;
            }
        }

        return false;
    }
}
