<?php
namespace Ttree\Identicons\Security;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Identicons".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

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
