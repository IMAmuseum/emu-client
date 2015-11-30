<?php
/* KE Software Open Source Licence
**
** Notice: Copyright (c) 2011-2013 KE SOFTWARE PTY LTD (ACN 006 213 298)
** (the "Owner"). All rights reserved.
**
** Licence: Permission is hereby granted, free of charge, to any person
** obtaining a copy of this software and associated documentation files
** (the "Software"), to deal with the Software without restriction,
** including without limitation the rights to use, copy, modify, merge,
** publish, distribute, sublicense, and/or sell copies of the Software,
** and to permit persons to whom the Software is furnished to do so,
** subject to the following conditions.
**
** Conditions: The Software is licensed on condition that:
**
** (1) Redistributions of source code must retain the above Notice,
**     these Conditions and the following Limitations.
**
** (2) Redistributions in binary form must reproduce the above Notice,
**     these Conditions and the following Limitations in the
**     documentation and/or other materials provided with the distribution.
**
** (3) Neither the names of the Owner, nor the names of its contributors
**     may be used to endorse or promote products derived from this
**     Software without specific prior written permission.
**
** Limitations: Any person exercising any of the permissions in the
** relevant licence will be taken to have accepted the following as
** legally binding terms severally with the Owner and any other
** copyright owners (collectively "Participants"):
**
** TO THE EXTENT PERMITTED BY LAW, THE SOFTWARE IS PROVIDED "AS IS",
** WITHOUT ANY REPRESENTATION, WARRANTY OR CONDITION OF ANY KIND, EXPRESS
** OR IMPLIED, INCLUDING (WITHOUT LIMITATION) AS TO MERCHANTABILITY,
** FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. TO THE EXTENT
** PERMITTED BY LAW, IN NO EVENT SHALL ANY PARTICIPANT BE LIABLE FOR ANY
** CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
** TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
** SOFTWARE OR THE USE OR OTHER DEALINGS WITH THE SOFTWARE.
**
** WHERE BY LAW A LIABILITY (ON ANY BASIS) OF ANY PARTICIPANT IN RELATION
** TO THE SOFTWARE CANNOT BE EXCLUDED, THEN TO THE EXTENT PERMITTED BY
** LAW THAT LIABILITY IS LIMITED AT THE OPTION OF THE PARTICIPANT TO THE
** REPLACEMENT, REPAIR OR RESUPPLY OF THE RELEVANT GOODS OR SERVICES
** (INCLUDING BUT NOT LIMITED TO SOFTWARE) OR THE PAYMENT OF THE COST OF SAME.
*/
require_once dirname(__FILE__) . '/../IMu.php';
require_once IMu::$lib . '/Exception.php';
require_once IMu::$lib . '/Handler.php';
require_once IMu::$lib . '/Session.php';
require_once IMu::$lib . '/Trace.php';

/**
 * @brief A helper class for implementing %IMu web services.
 * @code{.php}
 *   require_once IMu::$lib . '/Web/Service.php';
 * @endcode
 * @copyright 2011-2012 KE SOFTWARE PTY LTD
 * @version 2.0
 */
class IMuWebService
{
    /**
     * @details The base web service directory. Supplied in the constructor.
     * @var string $dir
     * @see __construct()
     */
	public $dir;

    /**
     * @details The full request URL. Generated at construction.
     * @var string $url
     * @see __construct()
     */
	public $url;

    /**
     * @details The configuration options. Loaded from files at construction.
     * @var array $config
     * @see __construct()
     * @see loadConfig()
     */
	public $config;

    /**
     * @details The HTTP GET & POST parameters. Generated at construction.
     * @var array $params
     * @see __construct()
     */
	public $params;

    /**
     * @details Determines the full request URL, loads all GET & POST parameters
     * to a common variable and loads config files relative to #$dir param.
     *
     * @param string $dir
     *   The base web service directory.
     * @see loadConfig()
     */
	public function
	__construct($dir)
	{
		$this->dir = $dir;

		$this->url = strtolower(preg_replace('/\/.*$/', '', $_SERVER['SERVER_PROTOCOL']));
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$this->url .= 's';
		$this->url .= '://' . $_SERVER['SERVER_NAME'];
		if ($_SERVER['SERVER_PORT'] != 80)
			$this->url .= ':' . $_SERVER['SERVER_PORT'];
		$this->url .= $_SERVER['REQUEST_URI'];

		$this->params = array();
		global $_GET;
		foreach ($_GET as $name => $value)
			$this->params[$name] = $value;
		global $_POST;
		foreach ($_POST as $name => $value)
			$this->params[$name] = $value;

		/* Configure */
		$config = array();

		/* ... defaults */
		$config['host'] = IMuSession::getDefaultHost();
		$config['port'] = IMuSession::getDefaultPort();

		/* ... service-specific */
		$this->loadConfig($config);

		$this->config = $config;
		if (array_key_exists('trace-file', $this->config))
			IMuTrace::setFile($this->config['trace-file']);
		if (array_key_exists('trace-level', $this->config))
			IMuTrace::setLevel($this->config['trace-level']);
	}

    /**
     * @details Remove and return the value for the HTTP parameter specified by
     * $name from #$params or just return $default if the parameter does not
     * exists.
     *
     * @param string $name
     *   The name of the parameter to remove and return.
     * @param string $default
     *   The value to return if no value exists for $name.
     *
     * @retval mixed
     *   The value of the parameter specified by $name or $default.
     */
	public function
	extractParam($name, $default = false)
	{
		if (! array_key_exists($name, $this->params))
			return $default;
		$value = $this->params[$name];
		unset($this->params[$name]);
		return $value;
	}

    /**
     * @details Return the value for the HTTP parameter specified by $name from
     * #$params or just return $default if the parameter does not exists.
     *
     * @param string $name
     *   The name of the parameter to return.
     * @param string $default
     *   The value to return if no value exists for $name.
     *
     * @retval mixed
     *   The value of the parameter specified by $name or $default.
     */
	public function
	getParam($name, $default = false)
	{
		if (! array_key_exists($name, $this->params))
			return $default;
		return $this->params[$name];
	}

    /**
     * @details Check for the presence of the HTTP parameter specified by $name
     * in #$params.
     *
     * @param string $name
     *   The name of the parameter to check.
     *
     * @retval boolean
     *   True if $name exists, false otherwise.
     */
	public function
	hasParam($name)
	{
		return array_key_exists($name, $this->params);
	}

    /**
     * @details Set the HTTP parameter specified by $name in #$params.
     *
     * @param string $name
     *   The name of the parameter to set.
     * @param string $value
     *   The value to set the parameter specified by $name.
     */
	public function
	setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

    /**
     * @details An empty method definition. The convention is to overridden this
     * method in a subclass and call it to process web service requests.
     *
     * @internal
     * @note This should probably be abstract or removed. I don't see any
     * value in this function. It could literally just be a convention to use
     * process() as the entry point to web service request processing.
     */
	public function
	process()
	{
		/* Do nothing by default */
	}

    /**
     * @details An IMuSession object. Only accessible after the connect()
     * method has been called.
     * @var IMuSession $session
     * @see connect()
     */
	protected $session;

    /**
     * @details Construct an IMuSession object and invoke its @link
     * IMuSession#connect() connect()@endlink method using the `host` and
     * `port` parameters of the #$config variable.
     *
     * @see $session
     */
	protected function
	connect()
	{
		$this->session = new IMuSession;
		$this->session->host = $this->config['host'];
		$this->session->port = $this->config['port'];
		$this->session->connect();
	}

    /**
     * @details Disconnect from the IMuSession.
     *
     * @see $session
     */
	protected function
	disconnect()
	{
		$this->session->disconnect();
	}

    /**
     * @details Load configuration options from the files:
     * - /ws/common/config.php
     * - /ws/client/config.php
     * - /ws/local/config.php
     *
     * relative to #$dir.
     *
     * @param array &$config
     * @see __construct()
     */
	protected function
	loadConfig(&$config)
	{
		@include $this->dir . '/ws/common/config.php';
		@include $this->dir . '/ws/client/config.php';
		@include $this->dir . '/ws/local/config.php';
	}
}

/**
 * @details Write an error to the trace file and throw an IMuException.
 * @note Optionally, any number of additional parameters can be supplied when
 * calling this function. Usually these are messages or values that provide
 * more information about the error that is being raised.
 *
 * Usage examples:
 * @code{.php}
 *   raise(400, 'UpdateNoFiles');
 *   raise(500, 'MultimediaTempFileOpen', $tempName);
 * @endcode
 *
 * @param int $code
 *   Usually the HTTP error code that should be used in the HTTP response.
 * @param string $id
 *   A string that identifies the exception.
 *
 * @throws IMuException
 */
function
raise($code, $id)
{
	$exception = new IMuException($id);

	$args = func_get_args();
	array_shift($args);
	array_shift($args);
	$exception->setArgs($args);

	$exception->setCode($code);

	IMuTrace::write(2, 'raising exception %s', $exception);
	throw $exception;
}
?>
