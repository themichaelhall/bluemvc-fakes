<?php

use BlueMvc\Core\Controller;
use BlueMvc\Core\View;

/**
 * A test controller.
 */
class TestController extends Controller
{
    /**
     * Index action.
     *
     * @return string The result.
     */
    public function indexAction()
    {
        return 'Hello World!';
    }

    /**
     * View action.
     *
     * @return View The result.
     */
    public function viewAction()
    {
        $this->setViewData('Foo', 'Bar');

        return new View('Baz');
    }
}
