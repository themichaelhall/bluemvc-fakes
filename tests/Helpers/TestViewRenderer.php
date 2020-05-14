<?php

declare(strict_types=1);

namespace BlueMvc\Fakes\Tests\Helpers;

use BlueMvc\Core\Base\AbstractViewRenderer;
use BlueMvc\Core\Interfaces\ApplicationInterface;
use BlueMvc\Core\Interfaces\Collections\ViewItemCollectionInterface;
use BlueMvc\Core\Interfaces\RequestInterface;
use DataTypes\Interfaces\FilePathInterface;

/**
 * A test view renderer.
 */
class TestViewRenderer extends AbstractViewRenderer
{
    /**
     * Constructs the view renderer.
     */
    public function __construct()
    {
        parent::__construct('view');
    }

    /**
     * Renders the view.
     *
     * @param ApplicationInterface             $application The application.
     * @param RequestInterface                 $request     The request.
     * @param FilePathInterface                $viewFile    The view file.
     * @param mixed|null                       $model       The model or null if there is no model.
     * @param ViewItemCollectionInterface|null $viewItems   The view items or null if there is no view data.
     *
     * @return string The rendered view.
     */
    public function renderView(ApplicationInterface $application, RequestInterface $request, FilePathInterface $viewFile, $model = null, ?ViewItemCollectionInterface $viewItems = null): string
    {
        $fileContent = file_get_contents($application->getViewPath()->withFilePath($viewFile)->__toString());
        $result = str_replace(
            [
                '{MODEL}',
                '{VIEWDATA}',
                '{URL}',
                '{TEMPDIR}',
            ],
            [
                $model !== null ? $model : '',
                $viewItems !== null ? implode(',', iterator_to_array($viewItems)) : '',
                $request->getUrl(),
                $application->getTempPath(),
            ],
            $fileContent
        );

        return $result;
    }
}
