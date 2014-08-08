<?php

class Curl
{
    private $handle = null;
    private $baseUri;
    private $header = null;

    public static function isAvailable()
    {
        return extension_loaded('curl');
    }

    public function __construct()
    {
        if (!self::isAvailable()) {
            throw new Exception('CURL extension is not loaded');
        }

        $this->handle = curl_init();
        $this->initOptions();
        $this->baseUri = new Uri();
        $this->header = new Header();
    }

    public function __destruct()
    {
        curl_close($this->handle);
    }

    public function __clone()
    {
        $request = new self;
        $request->handle = curl_copy_handle($this->handle);

        return $request;
    }

    private function initOptions()
    {
        $this->setOptions(array(
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_MAXREDIRS       => 20,
            CURLOPT_HEADER          => true,
            CURLOPT_PROTOCOLS       => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_USERAGENT       => 'Phalcon HTTP/1.0 (Curl)',
            CURLOPT_CONNECTTIMEOUT  => 30,
            CURLOPT_TIMEOUT         => 30
        ));
    }

    public function setOption($option, $value)
    {
        return curl_setopt($this->handle, $option, $value);
    }

    public function setOptions($options)
    {
        return curl_setopt_array($this->handle, $options);
    }

    public function setTimeout($timeout)
    {
        $this->setOption(CURLOPT_TIMEOUT, $timeout);
    }

    public function setConnectTimeout($timeout)
    {
        $this->setOption(CURLOPT_CONNECTTIMEOUT, $timeout);
    }

    private function send()
    {
        $header = array();
        if (count($this->header) > 0) {
            $header = $this->header->build();
        }
        $header[] = 'Expect:';
        $this->setOption(CURLOPT_HTTPHEADER, $header);

        $content = curl_exec($this->handle);

        if ($errno = curl_errno($this->handle)) {
            throw new HttpException(curl_error($this->handle), $errno);
        }

        $headerSize = curl_getinfo($this->handle, CURLINFO_HEADER_SIZE);

        $response = new Response();
        $response->header->parse(substr($content, 0, $headerSize));
        $response->body = substr($content, $headerSize);

        return $response;
    }

    /**
     * Prepare data for a cURL post.
     *
     * @param mixed   $params      Data to send.
     * @param boolean $useEncoding Whether to url-encode params. Defaults to true.
     *
     * @return void
     */
    private function initPostFields($params, $useEncoding = true)
    {
        if (is_array($params)) {
            foreach ($params as $param) {
                if (is_string($param) && preg_match('/^@/', $param)) {
                    $useEncoding = false;
                    break;
                }
            }

            if ($useEncoding) {
                $params = http_build_query($params);
            }
        }

        if (!empty($params)) {
            $this->setOption(CURLOPT_POSTFIELDS, $params);
        }
    }

    public function setProxy($host, $port = 8080, $user = null, $pass = null)
    {
        $this->setOptions(array(
            CURLOPT_PROXY     => $host,
            CURLOPT_PROXYPORT => $port
        ));

        if (!empty($user) && is_string($user)) {
            $pair = $user;
            if (!empty($pass) && is_string($pass)) {
                $pair .= ':' . $pass;
            }
            $this->setOption(CURLOPT_PROXYUSERPWD, $pair);
        }
    }

    public function get($uri, $params = array())
    {
        $uri = $this->resolveUri($uri);

        if (!empty($params)) {
            $uri->extendQuery($params);
        }

        $this->setOptions(array(
            CURLOPT_URL           => $uri->build(),
            CURLOPT_HTTPGET       => true,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));

        return $this->send();
    }

    public function head($uri, $params = array())
    {
        $uri = $this->resolveUri($uri);

        if (!empty($params)) {
            $uri->extendQuery($params);
        }

        $this->setOptions(array(
            CURLOPT_URL           => $uri->build(),
            CURLOPT_HTTPGET       => true,
            CURLOPT_CUSTOMREQUEST => 'HEAD'
        ));

        return $this->send();
    }

    public function delete($uri, $params = array())
    {
        $uri = $this->resolveUri($uri);

        if (!empty($params)) {
            $uri->extendQuery($params);
        }

        $this->setOptions(array(
            CURLOPT_URL           => $uri->build(),
            CURLOPT_HTTPGET       => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE'
        ));

        return $this->send();
    }

    public function post($uri, $params = array(), $useEncoding = true)
    {
        $this->setOptions(array(
            CURLOPT_URL           => $this->resolveUri($uri),
            CURLOPT_POST          => true,
            CURLOPT_CUSTOMREQUEST => 'POST'
        ));

        $this->initPostFields($params, $useEncoding);

        return $this->send();
    }

    public function put($uri, $params = array(), $useEncoding = true)
    {
        $this->setOptions(array(
            CURLOPT_URL           => $this->resolveUri($uri),
            CURLOPT_POST          => true,
            CURLOPT_CUSTOMREQUEST => 'PUT'
        ));

        $this->initPostFields($params, $useEncoding);

        return $this->send();
    }

    public function resolveUri($uri)
    {
        return $this->baseUri->resolve($uri);
    }
}

class Header implements \Countable
{
    protected static $messages = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    private $fields = array();
    public $version = '1.0';
    public $statusCode = 0;
    public $statusMessage = '';
    public $status = '';

    const BUILD_STATUS = 1;
    const BUILD_FIELDS = 2;

    public function set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    public function setMultiple($fields)
    {
        $this->fields = $fields;
    }

    public function addMultiple($fields)
    {
        $this->fields = array_combine($this->fields, $fields);
    }

    public function get($name)
    {
        return $this->fields[$name];
    }

    public function getAll()
    {
        return $this->fields;
    }

    /**
     * Determine if a header exists with a specific key.
     *
     * @param string $name Key to lookup.
     *
     * @return boolean
     */
    public function has($name)
    {
        foreach ($this->getAll() as $key => $value) {
            if (0 === strcmp(strtolower($key), strtolower($name))) {
                return true;
            }
        }
        return false;
    }

    public function remove($name)
    {
        unset($this->fields[$name]);
    }

    public function parse($content)
    {
        if (empty($content)) {
            return false;
        }

        if (is_string($content)) {
            $content = array_filter(explode("\r\n", $content));
        } elseif (!is_array($content)) {
            return false;
        }

        $status = array();
        if (preg_match('/^HTTP\/(\d(?:\.\d)?)\s+(\d{3})\s+(.+)$/i', $content[0], $status)) {
            $this->status = array_shift($content);
            $this->version = $status[1];
            $this->statusCode = intval($status[2]);
            $this->statusMessage = $status[3];
        }

        foreach ($content as $field) {
            if (!is_array($field)) {
                $field = array_map('trim', explode(':', $field, 2));
            }

            if (count($field) == 2) {
                $this->set($field[0], $field[1]);
            }
        }

        return true;
    }

    public function build($flags = 0)
    {
        $lines = array();
        if (($flags & self::BUILD_STATUS) && !empty(self::$messages[$this->statusCode])) {
            $lines[] = 'HTTP/' . $this->version . ' ' .
                $this->statusCode . ' ' .
                self::$messages[$this->statusCode];
        }

        foreach ($this->fields as $field => $value) {
            $lines[] = $field . ': ' . $value;
        }

        if ($flags & self::BUILD_FIELDS) {
            return implode("\r\n", $lines);
        }

        return $lines;
    }

    public function count()
    {
        return count($this->fields);
    }
}

class Response
{
    public $body = '';
    public $header = null;

    public function __construct()
    {
        $this->header = new Header();
    }
}

class Uri
{
    private $parts = array();

    public function __construct($uri = null)
    {
        if (empty($uri)) {
            return;
        }

        if (is_string($uri)) {
            $this->parts = parse_url($uri);
            if (!empty($this->parts['query'])) {
                $query = array();
                parse_str($this->parts['query'], $query);
                $this->parts['query'] = $query;
            }

            return;
        }

        if ($uri instanceof self) {
            $this->parts = $uri->parts;

            return;
        }

        if (is_array($uri)) {
            $this->parts = $uri;

            return;
        }

    }

    public function __toString()
    {
        return $this->build();
    }

    public function __unset($name)
    {
        unset($this->parts[$name]);
    }

    public function __set($name, $value)
    {
        $this->parts[$name] = $value;
    }

    public function __get($name)
    {
        return $this->parts[$name];
    }

    public function __isset($name)
    {
        return isset($this->parts[$name]);
    }

    public function build()
    {
        $uri = '';
        $parts = $this->parts;

        if (!empty($parts['scheme'])) {
            $uri .= $parts['scheme'] . ':';
            if (!empty($parts['host'])) {
                $uri .= '//';
                if (!empty($parts['user'])) {
                    $uri .= $parts['user'];

                    if (!empty($parts['pass'])) {
                        $uri .= ':' . $parts['pass'];
                    }

                    $uri .= '@';
                }
                $uri .= $parts['host'];
            }
        }

        if (!empty($parts['port'])) {
            $uri .= ':' . $parts['port'];
        }

        if (!empty($parts['path'])) {
            $uri .= $parts['path'];
        }

        if (!empty($parts['query'])) {
            $uri .= '?' . (is_array($parts['query']) ? http_build_query($parts['query']) : $parts['query']);
        }

        if (!empty($parts['fragment'])) {
            $uri .= '#' . $parts['fragment'];
        }

        return $uri;
    }

    public function resolve($uri)
    {
        $newUri = new self($this);
        $newUri->extend($uri);

        return $newUri;
    }

    public function extend($uri)
    {
        if (!$uri instanceof self) {
            $uri = new self($uri);
        }

        $this->parts = array_merge(
            $this->parts,
            array_diff_key($uri->parts, array_flip(array('query', 'path')))
        );

        if (!empty($uri->parts['query'])) {
            $this->extendQuery($uri->parts['query']);
        }

        if (!empty($uri->parts['path'])) {
            $this->extendPath($uri->parts['path']);
        }

        return $this;
    }

    public function extendQuery($params)
    {
        $query = empty($this->parts['query']) ? array() : $this->parts['query'];
        $params = empty($params) ? array() : $params;
        $this->parts['query'] = array_merge($query, $params);

        return $this;
    }

    public function extendPath($path)
    {
        if (empty($path)) {
            return $this;
        }

        if (!strncmp($path, '/', 1)) {
            $this->parts['path'] = $path;

            return $this;
        }

        if (empty($this->parts['path'])) {
            $this->parts['path'] = '/' . $path;

            return $this;
        }

        $this->parts['path'] = substr(
            $this->parts['path'],
            0,
            strrpos($this->parts['path'], '/') + 1
        ) . $path;

        return $this;
    }
}
