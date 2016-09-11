<?php

namespace BlueMvc\Fakes;

use BlueMvc\Core\Base\AbstractApplication;
use DataTypes\FilePath;
use DataTypes\Interfaces\FilePathInterface;

/**
 * BlueMvc fake application.
 */
class FakeApplication extends AbstractApplication
{
    /**
     * Constructs the fake application.
     *
     * @version 1.0.0
     *
     * @param FilePathInterface|null $documentRoot The document root or null to use the current directory.
     */
    public function __construct(FilePathInterface $documentRoot = null)
    {
        parent::__construct($documentRoot !== null ? $documentRoot : FilePath::parse(getcwd() . DIRECTORY_SEPARATOR));
    }

    /**
     * Sets the document root.
     *
     * @version 1.0.0
     *
     * @param FilePathInterface $documentRoot The document root.
     */
    public function setDocumentRoot(FilePathInterface $documentRoot)
    {
        parent::setDocumentRoot($documentRoot);
    }
}
