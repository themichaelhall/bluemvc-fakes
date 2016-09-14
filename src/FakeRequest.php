<?php
/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */
namespace BlueMvc\Fakes;

use BlueMvc\Core\Base\AbstractRequest;
use BlueMvc\Core\Http\Method;
use BlueMvc\Core\Interfaces\Http\MethodInterface;
use DataTypes\Interfaces\UrlInterface;

/**
 * BlueMvc fake request class.
 *
 * @since 1.0.0
 */
class FakeRequest extends AbstractRequest
{
    /**
     * Constructs the fake request.
     *
     * @since 1.0.0
     *
     * @param UrlInterface         $url    The url.
     * @param MethodInterface|null $method The method or null to use GET method.
     */
    public function __construct(UrlInterface $url, MethodInterface $method = null)
    {
        parent::__construct($url, $method !== null ? $method : new Method('GET'));
    }
}
