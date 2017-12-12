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
use BlueMvc\Core\Collections\RequestCookieCollection;
use BlueMvc\Core\Collections\UploadedFileCollection;
use BlueMvc\Core\Exceptions\Http\InvalidMethodNameException;
use BlueMvc\Core\Http\Method;
use BlueMvc\Core\Interfaces\Collections\HeaderCollectionInterface;
use BlueMvc\Core\Interfaces\Collections\ParameterCollectionInterface;
use BlueMvc\Core\Interfaces\Collections\RequestCookieCollectionInterface;
use BlueMvc\Core\Interfaces\RequestCookieInterface;
use BlueMvc\Core\UploadedFile;
use BlueMvc\Fakes\Exceptions\InvalidUploadedFileException;
use DataTypes\Exceptions\UrlInvalidArgumentException;
use DataTypes\FilePath;
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

        $url = Url::parseRelative($url, Url::parse('http://localhost/'));

        parent::__construct(
            $url,
            new Method($method),
            self::myParseHeaders($url),
            self::myParseQueryParameters($url->getQueryString()),
            new ParameterCollection(),
            new UploadedFileCollection(),
            new RequestCookieCollection()
        );
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
     * Sets a cookie.
     *
     * @since 1.0.0
     *
     * @param string                 $name   The cookie name.
     * @param RequestCookieInterface $cookie The cookie.
     *
     * @throws \InvalidArgumentException If the $name parameter is not a string.
     */
    public function setCookie($name, RequestCookieInterface $cookie)
    {
        parent::setCookie($name, $cookie);
    }

    /**
     * Sets the cookies.
     *
     * @since 1.0.0
     *
     * @param RequestCookieCollectionInterface $cookies
     */
    public function setCookies(RequestCookieCollectionInterface $cookies)
    {
        parent::setCookies($cookies);
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
     * Sets the headers.
     *
     * @since 1.0.0
     *
     * @param HeaderCollectionInterface $headers The headers.
     */
    public function setHeaders(HeaderCollectionInterface $headers)
    {
        parent::setHeaders($headers);
    }

    /**
     * Sets the raw content.
     *
     * @since 1.0.0
     *
     * @param string $content The content.
     *
     * @throws \InvalidArgumentException If the $content parameter is not a string.
     */
    public function setRawContent($content)
    {
        parent::setRawContent($content);
    }

    /**
     * Uploads a file.
     *
     * @since 1.0.0
     *
     * @param string $name     The name.
     * @param string $filename The filename.
     *
     * @throws \InvalidArgumentException    If any of the $name or $filename parameter is not a string.
     * @throws InvalidUploadedFileException If the filename is a non-existing file.
     */
    public function uploadFile($name, $filename)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('$name parameter is not a string.');
        }

        if (!is_string($filename)) {
            throw new \InvalidArgumentException('$filename parameter is not a string.');
        }

        $sourceFile = FilePath::parse($filename);
        if (!file_exists($sourceFile->__toString())) {
            throw new InvalidUploadedFileException('File "' . $sourceFile . '" does not exist.');
        }

        $destinationFile = tempnam(sys_get_temp_dir(), 'php');
        copy($sourceFile, $destinationFile);

        $this->setUploadedFile(
            $name,
            new UploadedFile(
                FilePath::parse($destinationFile),
                $sourceFile->__toString(),
                filesize($destinationFile)
            )
        );
    }

    /**
     * Destructor.
     *
     * @since 1.0.0
     */
    public function __destruct()
    {
        // Clean up uploaded files.
        foreach ($this->getUploadedFiles() as $uploadedFile) {
            unlink($uploadedFile->getPath()->__toString());
        }
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
