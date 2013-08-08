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
 * Description of ResponseHttp
 * @author t
 * @package Cocktail\Response
 * @version 1.01
 */
class ResponseHttp extends \Response {

	const STATUS_200_OK = '200 OK';
	const STATUS_201_CREATED = '201 Created';
	const STATUS_202_ACCEPTED = '202 Accepted';
	const STATUS_301_MOVED_PERMANENTLY = '301 Moved Permanently';
	const STATUS_302_FOUND = '302 Found';
	const STATUS_303_SEE_OTHER = '303 See Other';
	const STATUS_304_NOT_MODIFIED = '304 Not Modified';
	const STATUS_307_TEMPORARY_REDIRECT = '307 Temporary Redirect';
	const STATUS_400_BAD_REQUEST = '400 Bad Request';
	const STATUS_401_UNAUTHORIZED = '401 Unauthorized';
	const STATUS_403_FORBIDDEN = '403 Forbidden';
	const STATUS_404_NOT_FOUND = '404 Not Found';
	const STATUS_405_METHOD_NOT_ALLOWED = '405 Method Not Allowed';
	const STATUS_409_CONFLICT = '409 Conflict';
	const STATUS_410_GONE = '410 Gone';
	const STATUS_500_INTERNAL_SERVER_ERROR = '500 Internal Server Error';
	const STATUS_501_NOT_IMPLEMENTED = '501 Not Implemented';
	const STATUS_503_SERVICE_UNAVAILABLE = '503 Service Unavailable';

	/**
	 * @var array allowed protocols, to avoid confusion
	 */
	static protected $_protocols = array(
		'HTTP/1.0',
		'HTTP/1.1',
	);

	/**
	 * @var string communication protocol
	 */
	protected $_protocol = 'HTTP/1.1';

	/**
	 * @var int http response status
	 */
	protected $_status;

	/**
	 * @var array accumulate valid headers in this array. The values shall be converted to arrays upon setting
	 */
	protected $_headers = array(
		'Access-Control-Allow-Origin' => null,
		'Age' => null,
		'Allow' => null,
		'Cache-Control' => null,
		'Connection' => null,
		'Content-Encoding' => null,
		'Content-Language' => null,
		'Content-Length' => null,
		'Content-Location' => null,
		'Content-MD5' => null,
		'Content-Disposition' => null,
		'Content-Range' => null,
		'Content-Type' => null,
		'Date' => null,
		'ETag' => null,
		'Expires' => null,
		'Last-Modified' => null,
		'Link' => null,
		'Location' => null,
		'Pragma' => null,
		'Refresh' => null,
		'Retry-After' => null,
		'Server' => null,
		'Set-Cookie' => null,
		'Transfer-Encoding' => null,
	);

	/**
	 * @var array cookies to be set
	 */
	protected $_cookies = array();

	/**
	 * @var string the actual content is accumulated in this
	 */
	protected $_content = '';

	/**
	 * I am protected, use get()
	 */
	protected function __construct() {}

	/**
	 * I send (output) the response
	 * @throws \RuntimeException
	 */
	public function send() {

		if (!empty($this->_cookies)) {
			foreach ($this->_cookies AS $cookie) {
				// @todo add setcookies here, preferably define Cookie object for storage
				die('@TODO');
			}
		}

		foreach ($this->_headers AS $key=>$header) {
			if (is_null($header)) {
				continue;
			}
			elseif (is_array($header)) {
				foreach ($header AS $each_header) {
					header($key . ': ' . $each_header);
				}
			}
			else {
				throw new \RuntimeException ('invalid header');
			}
		}

		if (is_object($this->_content)) {
			$mode = \Beautify::getMode();
			if ($mode == \Beautify::MODE_BEAUTIFUL_NEWLINES) {
				$mode = \Beautify::MODE_BEAUTIFUL;
			}
			echo \Beautify::beauty($this->_content, 0, $mode);
		}
		else {
			echo $this->_content;
		}

	}

	public function setProtocol($protocol) {
		if (!in_array($protocol, self::$_protocols)) {
			throw new \InvalidArgumentException('protocol ' . $protocol . ' unknown');
		}
		$this->_protocol = $protocol;
	}

	public function sendRedirect($location, $status=303) {
		$statusHeader = $this->_protocol . ' ';
		switch ($status) {
			case 301:
				$response.= self::STATUS_301_MOVED_PERMANENTLY;
				break;
			case 302:
				$response.= self::STATUS_302_FOUND;
				break;
			case 303:
				$response.= self::STATUS_303_SEE_OTHER;
				break;
			case 307:
				$response.= self::STATUS_307_TEMPORARY_REDIRECT;
				break;
			default:
				throw new \InvalidArgumentException('status ' . $status . ' not allowed in ResponseHttp->sendRedirect');
		}
		if (headers_sent()) {
			echo $response . "\n" .
					'Location: ' . $location;
			exit;
		}
		else {
			header($response);
			header('Location: ' . $location);
			exit;
		}
	}

	public function sendNotImplemented() {
		header($this->_protocol . self::STATUS_501_NOT_IMPLEMENTED);
		echo $this->_content;
		exit;
	}

	public function sendNotFound() {
		header($this->_protocol . ' ' . self::STATUS_404_NOT_FOUND);
		echo $this->_content;
		exit;
	}

}
