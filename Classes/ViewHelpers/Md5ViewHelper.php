<?php

namespace Ttree\Identicons\ViewHelpers;

use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

class Md5ViewHelper extends AbstractViewHelper
{
    public function render($hash) {
        return md5($hash);
    }
}
