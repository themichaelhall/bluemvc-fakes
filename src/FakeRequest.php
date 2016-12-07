<?php
/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */

namespace BlueMvc\Fakes;

use BlueMvc\Core\Base\AbstractRequest;
use BlueMvc\Core\Exceptions\Http\InvalidMethodNameException;
use BlueMvc\Core\Http\Method;
use DataTypes\Exceptions\UrlInvalidArgumentException;
use DataTypes\Url;

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
     * @param string $url    The url.
     * @param string $method The method or null to use GET method.
     *
     * @throws \InvalidArgumentException   If any of the parameters are of invalid type.
     * @throws InvalidMethodNameException  If the method parameter is not a valid method.
     * @throws UrlInvalidArgumentException If the url parameter is not a valid Url.
     */
    public function __construct($url, $method = 'GET')
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException('$url parameter is not a string.');
        }

        if (!is_string($method)) {
            throw new \InvalidArgumentException('$method parameter is not a string.');
        }

        parent::__construct(Url::parse($url), new Method($method));
    }
}
