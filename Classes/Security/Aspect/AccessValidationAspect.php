<?php
namespace Ttree\Identicons\Security\Aspect;

use Ttree\Identicons\Security\AccessValidationInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;

/**
 * Access Validation Aspect
 *
 * @Flow\Aspect
 * @Flow\Scope("singleton")
 */
class AccessValidationAspect
{
    /**
     * @var AccessValidationInterface
     * @Flow\Inject
     */
    protected $accessValidation;

    /**
     * @Flow\Before("setting(Ttree.Identicons.access.enable) && method(public Ttree\Identicons\Controller\IdenticonsController->generateAction())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return string
     */
    public function check(JoinPointInterface $joinPoint)
    {
        $hash = $joinPoint->getMethodArgument('hash');
        $this->accessValidation->check($hash);
    }
}
