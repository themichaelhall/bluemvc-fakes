<?php

declare(strict_types=1);

namespace BlueMvc\Fakes\Tests\Helpers;

use BlueMvc\Core\ErrorController;
use BlueMvc\Core\View;

/**
 * An error test controller.
 */
class TestErrorController extends ErrorController
{
    /**
     * Default action.
     *
     * @param string $statusCode The status code.
     *
     * @return View The view.
     */
    public function defaultAction(string $statusCode): View
    {
        return new View($statusCode);
    }
}
