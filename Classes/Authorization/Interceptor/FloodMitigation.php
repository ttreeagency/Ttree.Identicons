<?php
namespace Ttree\Identicons\Authorization\Interceptor;

use Ttree\Identicons\Service\FloodMitigationService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\Request;
use Neos\Flow\Security\Authorization\InterceptorInterface;
use Neos\Flow\Security\Exception\AccessDeniedException;

/**
 * Flood Mitigation
 *
 * @Flow\Scope("singleton")
 */
class FloodMitigation implements InterceptorInterface
{
    /**
     * @var FloodMitigationService
     * @Flow\Inject
     */
    protected $floodMitigationService;

    /**
     * @var Bootstrap
     * @Flow\Inject
     */
    protected $bootstrap;

    /**
     * Invokes nothing, always throws an AccessDenied Exception.
     *
     * @return boolean Always returns FALSE
     * @throws AccessDeniedException
     */
    public function invoke()
    {
        /** @var Request $request */
        $request = $this->bootstrap->getActiveRequestHandler()->getHttpRequest();
        $this->floodMitigationService->registerAccess($request->getClientIpAddress());

        return true;
    }
}
