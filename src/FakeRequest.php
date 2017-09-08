<?php
/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */

namespace BlueMvc\Fakes;

use BlueMvc\Core\Base\AbstractRequest;
use BlueMvc\Core\Collections\HeaderCollection;
use BlueMvc\Core\Collections\ParameterCollection;
use BlueMvc\Core\Exceptions\Http\InvalidMethodNameException;
use BlueMvc\Core\Http\Method;
use BlueMvc\Core\Interfaces\Collections\HeaderCollectionInterface;
use BlueMvc\Core\Interfaces\Collections\ParameterCollectionInterface;
use BlueMvc\Core\Interfaces\Collections\UploadedFileCollectionInterface;
use DataTypes\Exceptions\UrlInvalidArgumentException;
use DataTypes\Interfaces\UrlInterface;
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
    public function __construct($url = '/', $method = 'GET')
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException('$url parameter is not a string.');
        }

        if (!is_string($method)) {
            throw new \InvalidArgumentException('$method parameter is not a string.');
        }

        parent::__construct(Url::parseRelative($url, Url::parse('http://localhost/')), new Method($method));

        $this->setHeaders(self::myParseHeaders($this->getUrl()));
        $this->setQueryParameters(self::myParseQueryParameters($this->getUrl()->getQueryString()));
    }

    /**
     * Adds a header.
     *
     * @since 1.0.0
     *
     * @param string $name  The name.
     * @param string $value The value.
     */
    public function addHeader($name, $value)
    {
        parent::addHeader($name, $value);
    }

    /**
     * Sets a form parameter.
     *
     * @since 1.0.0
     *
     * @param string $name  The form parameter name.
     * @param string $value The form parameter value.
     *
     * @throws \InvalidArgumentException If any of the parameters are of invalid type.
     */
    public function setFormParameter($name, $value)
    {
        parent::setFormParameter($name, $value);
    }

    /**
     * Sets the form parameters.
     *
     * @since 1.0.0
     *
     * @param ParameterCollectionInterface $parameters The form parameters.
     */
    public function setFormParameters(ParameterCollectionInterface $parameters)
    {
        parent::setFormParameters($parameters);
    }

    /**
     * Sets a header.
     *
     * @since 1.0.0
     *
     * @param string $name  The name.
     * @param string $value The value.
     */
    public function setHeader($name, $value)
    {
        parent::setHeader($name, $value);
    }

    /**
     * Sets the uploaded files.
     *
     * @since 1.0.0
     *
     * @param UploadedFileCollectionInterface $uploadedFiles The uploaded files.
     */
    public function setUploadedFiles(UploadedFileCollectionInterface $uploadedFiles)
    {
        parent::setUploadedFiles($uploadedFiles);
    }

    /**
     * Parses a url into a header collection.
     *
     * @param UrlInterface $url The url.
     *
     * @return HeaderCollectionInterface The header collection.
     */
    private static function myParseHeaders(UrlInterface $url)
    {
        $result = new HeaderCollection();
        $result->set('Host', $url->getHostAndPort());

        return $result;
    }

    /**
     * Parses a query string into a parameter collection.
     *
     * @param string|null $queryString The query string or null if no query string is set.
     *
     * @return ParameterCollectionInterface The parameter collection.
     */
    private static function myParseQueryParameters($queryString)
    {
        $parameters = new ParameterCollection();

        if ($queryString === null) {
            return $parameters;
        }

        parse_str($queryString, $parametersArray);
        foreach ($parametersArray as $parameterName => $parameterValue) {
            $parameters->set($parameterName, is_array($parameterValue) ? $parameterValue[0] : $parameterValue);
        }

        return $parameters;
    }
}
