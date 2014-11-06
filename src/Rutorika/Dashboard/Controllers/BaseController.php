<?php

namespace Rutorika\Dashboard\Controllers;

/**
 * Class BaseController
 *
 * @package Rutorika\Dashboard\Controllers\Admin
 */
class BaseController extends \Controller
{
    public $layoutName = 'dashboard::layout';

    protected function setupLayout()
    {
        $this->layout = \View::make($this->layoutName);
    }

    protected function _populateView($viewName, $viewParams, $section = 'content')
    {
        $this->layout->nest($section, $viewName, $viewParams);
    }
}