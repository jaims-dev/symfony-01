<?php


namespace App\Security;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct( AccessDecisionManagerInterface $accessDecisionManager)
    {
        /* needed for ROLE_ADMIN*/
        $this->accessDecisionManager = $accessDecisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if( !in_array($attribute, [self::DELETE, self::EDIT])) {
            return false;
        }

        if( !$subject instanceof MicroPost) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if($this->accessDecisionManager->decide($token, [USER::ROLE_ADMIN])) {
            return true;
        }
        
        $authenticatedUser = $token->getUser();
        if(!$authenticatedUser instanceof User) {
            return false;
        }

        /**
         * @var MicroPost $micropost
         *
         * This assignment only because I want type-hinting provided by my IDE :-)
         */
        $micropost = $subject;

        return $micropost->getUser()->getId() == $authenticatedUser->getId();
    }
}

