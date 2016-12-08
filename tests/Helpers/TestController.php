<?php

use BlueMvc\Core\ActionResults\PermanentRedirectResult;
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

    /**
     * Action result action.
     *
     * @return PermanentRedirectResult The result.
     */
    public function actionResultAction()
    {
        return new PermanentRedirectResult('https://localhost/');
    }
}
