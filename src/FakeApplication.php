<?php

/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */

declare(strict_types=1);

namespace BlueMvc\Fakes;

use BlueMvc\Core\Base\AbstractApplication;
use BlueMvc\Core\Exceptions\InvalidFilePathException;
use DataTypes\System\Exceptions\FilePathInvalidArgumentException;
use DataTypes\System\FilePath;

/**
 * BlueMvc fake application.
 *
 * @since 1.0.0
 */
class FakeApplication extends AbstractApplication
{
    /**
     * Constructs the fake application.
     *
     * @since 1.0.0
     *
     * @param string|null $documentRoot The document root or null to use the current directory.
     *
     * @throws FilePathInvalidArgumentException If the document root parameter is not a valid file path.
     * @throws InvalidFilePathException         If the document root parameter is invalid.
     */
    public function __construct(?string $documentRoot = null)
    {
        if ($documentRoot === null) {
            $documentRoot = getcwd();
        }

        if (substr($documentRoot, -1) !== DIRECTORY_SEPARATOR) {
            $documentRoot .= DIRECTORY_SEPARATOR;
        }

        parent::__construct(FilePath::parse($documentRoot));
    }

    /**
     * Sets the debug mode.
     *
     * @since 1.0.0
     *
     * @param bool $isDebug The debug mode.
     */
    public function setDebug(bool $isDebug): void
    {
        parent::setDebug($isDebug);
    }
}
