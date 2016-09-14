<?php
/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */
namespace BlueMvc\Fakes;

use BlueMvc\Core\Base\AbstractApplication;
use DataTypes\FilePath;
use DataTypes\Interfaces\FilePathInterface;

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
     * @param FilePathInterface|null $documentRoot The document root or null to use the current directory.
     */
    public function __construct(FilePathInterface $documentRoot = null)
    {
        parent::__construct($documentRoot !== null ? $documentRoot : FilePath::parse(getcwd() . DIRECTORY_SEPARATOR));
    }

    /**
     * Sets the document root.
     *
     * @since 1.0.0
     *
     * @param FilePathInterface $documentRoot The document root.
     */
    public function setDocumentRoot(FilePathInterface $documentRoot)
    {
        parent::setDocumentRoot($documentRoot);
    }
}
