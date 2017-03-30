<?php

use BlueMvc\Core\ActionResults\ForbiddenResult;
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

    /**
     * Pre-action event.
     *
     * @return string|null The result.
     */
    protected function onPreActionEvent()
    {
        parent::onPreActionEvent();

        if ($this->getRequest()->getHeader('X-Trigger-PreActionEvent') !== null) {
            return 'Pre-action event triggered.';
        }

        return null;
    }

    /**
     * Post-action event.
     *
     * @return ForbiddenResult|null The result.
     */
    protected function onPostActionEvent()
    {
        parent::onPostActionEvent();

        if ($this->getRequest()->getHeader('X-Trigger-PostActionEvent') !== null) {
            return new ForbiddenResult('Post-action event triggered.');
        }

        return null;
    }
}
