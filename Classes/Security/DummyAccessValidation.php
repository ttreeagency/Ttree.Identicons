<?php
namespace Ttree\Identicons\Security;

/**
 * Dummy Access Validation
 *
 * @package Ttree\Identicons\Security
 */
class DummyAccessValidation implements AccessValidationInterface
{
    /**
     * {@inheritdoc}
     */
    public function check($hash)
    {
    }
}
