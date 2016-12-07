<?php
/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */

namespace BlueMvc\Fakes;

use BlueMvc\Core\Base\AbstractApplication;
use BlueMvc\Core\Exceptions\InvalidFilePathException;
use DataTypes\Exceptions\FilePathInvalidArgumentException;
use DataTypes\FilePath;

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
     * @throws \InvalidArgumentException        If the document root parameter is not a string or null.
     * @throws InvalidFilePathException         If the document root parameter is invalid.
     */
    public function __construct($documentRoot = null)
    {
        if (!is_string($documentRoot) && !(is_null($documentRoot))) {
            throw new \InvalidArgumentException('$documentRoot parameter is not a string or null.');
        }

        if ($documentRoot === null) {
            $documentRoot = getcwd();
        }

        if (substr($documentRoot, -1) !== DIRECTORY_SEPARATOR) {
            $documentRoot .= DIRECTORY_SEPARATOR;
        }

        parent::__construct(FilePath::parse($documentRoot));
    }
}
