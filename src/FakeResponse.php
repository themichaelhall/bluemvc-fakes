<?php
/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */
namespace BlueMvc\Fakes;

use BlueMvc\Core\Base\AbstractResponse;

/**
 * BlueMvc fake response class.
 *
 * @since 1.0.0
 */
class FakeResponse extends AbstractResponse
{
    /**
     * Does nothing.
     *
     * @since 1.0.0
     */
    public function output()
    {
    }
}
