<?php

declare(strict_types=1);

namespace BlueMvc\Fakes\Tests\Helpers;

use BlueMvc\Core\ActionResults\ForbiddenResult;
use BlueMvc\Core\ActionResults\JsonResult;
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
        $this->setViewItem('Foo', 'Bar');

        return new View('Baz');
    }

    /**
     * Custom view action.
     *
     * @return View The result.
     */
    public function customViewAction()
    {
        $this->setViewItem('Foo', 'Bar');

        return new View('Baz', 'custom');
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
     * Json result action.
     *
     * @return JsonResult The result.
     */
    public function jsonResultAction()
    {
        return new JsonResult(['Foo' => 'Bar']);
    }

    /**
     * Action throwing exception.
     *
     * @throws \DomainException The exception.
     */
    public function exceptionAction()
    {
        throw new \DomainException('Throwing exception!');
    }

    /**
     * Action returning a scalar.
     *
     * @return float The result.
     */
    public function scalarAction()
    {
        return 12.5;
    }

    /**
     * Action returning null.
     *
     * @return null The result.
     */
    public function nullAction()
    {
        return null;
    }

    /**
     * Action that sets a session item and returns the current session items.
     *
     * @return string The result.
     */
    public function sessionAction()
    {
        if ($this->getRequest()->getMethod()->isPost()) {
            $this->getRequest()->setSessionItem(
                $this->getRequest()->getFormParameter('Name'),
                $this->getRequest()->getFormParameter('Value')
            );
        }

        $result = [];
        foreach ($this->getRequest()->getSessionItems() as $itemKey => $itemValue) {
            $result[] = $itemKey . '=' . $itemValue;
        }

        return implode(',', $result);
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
