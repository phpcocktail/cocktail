<?php
/**
 * This file is part of PhpCocktail. PhpCocktail is free software: you can redistribute it and/or modify it under the
 * 		terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3
 * 		of the License, or (at your option) any later version.
 * PhpCocktail is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * 		warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * 		more details. You should have received a copy of the GNU Lesser General Public License along with PhpCocktail.
 * 		If not, see <http://www.gnu.org/licenses/>.
 * @copyright Copyright 2013 t
 */
namespace Cocktail;

/**
 * RequestHttp is both a subclass of Request and decorator for HttpRequest (PECL package), if that is available
 *	subclass of Request, for transparent object handling wherever possible. Eg. same code can be run by a http request
 *		or shell command
 * @author t
 * @package Cocktail\Request
 * @version 1.01
 * @property-read array $REQUEST
 * @property-read array $GET
 * @property-read array $POST
 * @property-read array $COOKIE
 * @property-read boolean $isHttps
 * @property-read 'http://'|'https://' $schema
 * @property-read string $host
 * @property-read string $baseUrl eg. http://api.yourdomain.com
 * @property-read string $pathinfo eg. ...
 * @property-read string $requestUri eg. ...
 * @property-read string $serverProtocol
 * @property-read string $requestMethod GET, POST etc.
 * @property-read string $isAjax
 * @property-read string $remoteAddress
 * @property-read string $userAgent
 * @property-read string $accept
 * @property-read string $acceptLanguage
 * @property-read int $tstamp
 */
class RequestHttp extends \Request {

	protected static $_Instance;

	protected $_REQUEST;
	protected $_GET;
	protected $_POST;
	protected $_COOKIE;
	const ORIGIN_REQUEST = 'REQUEST';
	const ORIGIN_GET = 'GET';
	const ORIGIN_POST = 'POST';
	const ORIGIN_COOKIE = 'COOKIE';

	protected $_isHttps = false;
	protected $_schema = 'http://';
	protected $_host;
	protected $_baseUrl;
	protected $_pathInfo;
	protected $_requestUri;

	protected $_serverProtocol = 'HTTP/1.1';
	protected $_requestMethod = 'GET';
	const REQUESTMETHOD_GET = 'GET';
	const REQUESTMETHOD_POST = 'POST';
	const REQUESTMETHOD_PUT = 'POT';
	const REQUESTMETHOD_DELETE = 'DELETE';
	const REQUESTMETHOD_HEAD = 'HEAD';
	protected $_isAjax = false;

	protected $_remoteAddress;
	protected $_userAgent;
	protected $_accept;
	protected $_acceptLanguage;

	#protected $_connection;
	protected $_tstamp;

	protected $_requestedExtension = null;

	/**
	 * @var array[string]mixed simple data registry array
	 */
	protected $_is = array();

	/**
	 * I return the object for the current HTTP request.
	 * @return \RequestHttp
	 */
	protected static function _instance() {

		$Request = parent::_instance();
		$Request->_REQUEST = $_REQUEST;
		$Request->_GET = $_GET;
		$Request->_POST = $_POST;
		$Request->_COOKIE = $_COOKIE;

		$Request->_isHttps = empty($_SERVER['HTTPS']) || !$_SERVER['HTTPS'] ? false : true;
		$Request->_schema = $Request->_isHttps ? 'https://' : 'http://';
		$Request->_host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
		$Request->_baseUrl = $Request->_schema . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null);
		$Request->_pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
		$Request->_requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;

		$Request->_serverProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : null;
		$Request->_requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
		$Request->_isAjax = isset($_SERVER['X_REQUESTED_WITH']) && ($_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;

		$Request->_remoteAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
		$Request->_userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
		$Request->_accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : null;
		$Request->_acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;

		$routeParts = trim($Request->_requestUri, '/&#');
		if (substr($routeParts, -5) == '.html') {
			$Request->_requestedExtension = 'html';
			$routeParts = substr($routeParts, 0, -5);
		}
		// @todo this should come from a \Camarera::conf()
		$basePath = \Application::instance()->getConfig()->basePath;
		if (!empty($basePath) && (substr($routeParts, 0, strlen($basePath)) == $basePath)) {
			$routeParts = '' . substr($routeParts, strlen($basePath));
		}
		$routeParts = trim($routeParts, '/');
		$routeParts = strlen($routeParts) ? explode('/', $routeParts) : array();

		$Request->_routeParts = $routeParts;

		return $Request;
	}

	/**
	 * I return param(s)
	 * @param null $paramName
	 * @param null $origin possible as self::ORIGIN_XXX and defaults to 'REQUEST' if empty or invalid
	 * @return mixed|null
	 */
	public function param($paramName=null, $origin=null) {
		$origin = strtoupper($origin);
		if (!in_array($origin, array('GET','POST','COOKIE'))) {
			$origin = 'REQUEST';
		}
		return parent::param($paramName, $origin);
	}

}
