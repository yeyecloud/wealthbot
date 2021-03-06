<?php

namespace Wealthbot\RiaBundle\Service\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Wealthbot\ClientBundle\Entity\SystemAccount;
use Wealthbot\UserBundle\Entity\User;
use Wealthbot\ClientBundle\Entity\BillItem;

class BillItemVoter extends BaseVoter
{
    const PERMISSION_CHANGE_FEE  = 'CHANGE_FEE';

    /**
     * @param TokenInterface $token
     * @param object $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ( ! $object instanceof BillItem) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();
        if ( ! $user instanceof User) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if ($user->isAdmin()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        if (in_array($attributes[0], array(static::PERMISSION_CHANGE_FEE))) {
            // Check system account
            $systemAccount = $object->getSystemAccount();
            if ( ! $systemAccount instanceof SystemAccount) {
                return VoterInterface::ACCESS_ABSTAIN;
            }

            if ($user == $systemAccount->getClient()->getRia()) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}