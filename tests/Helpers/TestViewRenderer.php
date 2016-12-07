<?php

use BlueMvc\Core\Base\AbstractViewRenderer;
use BlueMvc\Core\Interfaces\ApplicationInterface;
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
     * @param ApplicationInterface $application    The application.
     * @param FilePathInterface    $viewsDirectory The views directory.
     * @param FilePathInterface    $viewFile       The view file.
     * @param mixed                $model          The model or null if there is no model.
     * @param mixed                $viewData       The view data or null if there is no view data.
     *
     * @return string The rendered view.
     */
    public function renderView(ApplicationInterface $application, FilePathInterface $viewsDirectory, FilePathInterface $viewFile, $model = null, $viewData = null)
    {
        $fileContent = file_get_contents($viewsDirectory->withFilePath($viewFile));
        $result = str_replace(
            [
                '{MODEL}',
                '{VIEWDATA}',
            ],
            [
                $model !== null ? $model : '',
                $viewData !== null ? (is_array($viewData) ? implode(',', $viewData) : $viewData) : '',
            ], $fileContent);

        return $result;
    }
}