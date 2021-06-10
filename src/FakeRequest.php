<?php

/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */

declare(strict_types=1);

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
use BlueMvc\Core\Interfaces\Collections\SessionItemCollectionInterface;
use BlueMvc\Core\Interfaces\Http\MethodInterface;
use BlueMvc\Core\Interfaces\RequestCookieInterface;
use BlueMvc\Core\UploadedFile;
use BlueMvc\Fakes\Collections\FakeSessionItemCollection;
use BlueMvc\Fakes\Exceptions\InvalidUploadedFileException;
use DataTypes\Exceptions\UrlInvalidArgumentException;
use DataTypes\FilePath;
use DataTypes\Interfaces\IPAddressInterface;
use DataTypes\Interfaces\UrlInterface;
use DataTypes\IPAddress;
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
     * @param string $method The method.
     *
     * @throws InvalidMethodNameException  If the method parameter is not a valid method.
     * @throws UrlInvalidArgumentException If the url parameter is not a valid Url.
     */
    public function __construct(string $url = '/', string $method = 'GET')
    {
        $url = Url::parseRelative($url, Url::parse('http://localhost/'));
        $method = new Method(strtoupper($method));

        parent::__construct(
            $url,
            $method,
            new HeaderCollection(),
            self::parseQueryParameters($url->getQueryString()),
            new ParameterCollection(),
            new UploadedFileCollection(),
            new RequestCookieCollection(),
            new FakeSessionItemCollection()
        );

        $this->updateHeadersFromUrl($url);
        $this->setClientIp(IPAddress::fromParts([127, 0, 0, 1]));
    }

    /**
     * Adds a header.
     *
     * @since 1.0.0
     *
     * @param string $name  The name.
     * @param string $value The value.
     */
    public function addHeader(string $name, string $value): void
    {
        parent::addHeader($name, $value);
    }

    /**
     * Sets the client IP address.
     *
     * @since 1.1.0
     *
     * @param IPAddressInterface $clientIp The client IP address.
     */
    public function setClientIp(IPAddressInterface $clientIp): void
    {
        parent::setClientIp($clientIp);
    }

    /**
     * Sets a cookie.
     *
     * @since 1.0.0
     *
     * @param string                 $name   The cookie name.
     * @param RequestCookieInterface $cookie The cookie.
     */
    public function setCookie(string $name, RequestCookieInterface $cookie): void
    {
        parent::setCookie($name, $cookie);
    }

    /**
     * Sets the cookies.
     *
     * @since 1.0.0
     *
     * @param RequestCookieCollectionInterface $cookies The cookies.
     */
    public function setCookies(RequestCookieCollectionInterface $cookies): void
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
     */
    public function setFormParameter(string $name, string $value): void
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
    public function setFormParameters(ParameterCollectionInterface $parameters): void
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
    public function setHeader(string $name, string $value): void
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
    public function setHeaders(HeaderCollectionInterface $headers): void
    {
        parent::setHeaders($headers);
    }

    /**
     * Sets the method.
     *
     * @since 2.1.0
     *
     * @param MethodInterface $method The method.
     */
    public function setMethod(MethodInterface $method): void
    {
        parent::setMethod($method);
    }

    /**
     * Sets the raw content.
     *
     * @since 1.0.0
     *
     * @param string $content The content.
     */
    public function setRawContent(string $content): void
    {
        parent::setRawContent($content);
    }

    /**
     * Sets the session items.
     *
     * @since 2.0.0
     *
     * @param SessionItemCollectionInterface $sessionItems The session items.
     */
    public function setSessionItems(SessionItemCollectionInterface $sessionItems): void
    {
        parent::setSessionItems($sessionItems);
    }

    /**
     * Sets the url.
     *
     * @since 2.1.0
     *
     * @param UrlInterface $url The url.
     */
    public function setUrl(UrlInterface $url): void
    {
        parent::setUrl($url);

        $this->updateHeadersFromUrl($url);
    }

    /**
     * Uploads a file.
     *
     * @since 1.0.0
     *
     * @param string $name     The name.
     * @param string $filename The filename.
     *
     * @throws InvalidUploadedFileException If the filename is a non-existing file.
     */
    public function uploadFile(string $name, string $filename): void
    {
        $sourceFile = FilePath::parse($filename);
        if (!file_exists($sourceFile->__toString())) {
            throw new InvalidUploadedFileException('File "' . $sourceFile . '" does not exist.');
        }

        $destinationFile = tempnam(sys_get_temp_dir(), 'php');
        copy($sourceFile->__toString(), $destinationFile);

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
     * Updates headers from url.
     *
     * @param UrlInterface $url The url.
     */
    private function updateHeadersFromUrl(UrlInterface $url): void
    {
        $this->setHeader('Host', $url->getHostAndPort());
    }

    /**
     * Parses a query string into a parameter collection.
     *
     * @param string|null $queryString The query string or null if no query string is set.
     *
     * @return ParameterCollectionInterface The parameter collection.
     */
    private static function parseQueryParameters(?string $queryString): ParameterCollectionInterface
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
