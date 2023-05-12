<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Customer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CustomerVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Customer;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // you know $subject is a User object, thanks to `supports()`
        /** @var Customer $customer */
        $customer = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($customer, $user),
            self::EDIT => $this->canEdit($customer, $user),
            self::DELETE => $this->canDelete($customer, $user),
            default => throw new \LogicException('Ce voteur ne devrait pas être atteint.')
        };
    }

    private function canView(Customer $customer, User $user): bool
    {
        return $user === $customer->getMarketplace();
    }

    private function canEdit(Customer $customer, User $user): bool
    {
        return $user === $customer->getMarketplace();
    }

    private function canDelete(Customer $customer, User $user): bool
    {
        return $user === $customer->getMarketplace();
    }
}
