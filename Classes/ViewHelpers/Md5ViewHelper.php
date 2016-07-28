<?php

namespace Ttree\Identicons\ViewHelpers;


use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class Md5ViewHelper extends AbstractViewHelper
{
    public function render($hash) {
        return md5($hash);
    }
}
