<?php
namespace Ttree\Identicons\Security;

/**
 * Access Validation Interface
 *
 * If you use the access validation feature, you need to implement this interface.
 *
 * The check method must throw exception like throw new AccessDeniedException if
 * you need to limit the access to the controller action.
 *
 * @package Ttree\Identicons\Security
 */
interface AccessValidationInterface
{
    /**
     * @param string $hash
     * @return void
     */
    public function check($hash);
}
