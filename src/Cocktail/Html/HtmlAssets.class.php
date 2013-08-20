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
 * I manage html assets - js, css and return in a form suitable for rendering in a html source
 * @author t
 * @package Cocktail\Html
 * @version 1.01
 */
class HtmlAssets {

	const RAW_JS_PATTERN = '/(\(|\{|\[)/';
	const RAW_CSS_PATTERN = '/(\{)|(:.+;)/';

	protected static $_instance;

	protected $_js = array(
			'files' => array(),
			'head' => '',
			'onLoad' => '',
			'bodyEnd' => '',
	);

	protected $_css = array(
			'files' => array(),
			'head' => '',
	);

	protected $_version = null;

	/**
	 * I return singleton instance
	 */
	public static function instance() {
		if (is_null(static::$_instance)) {
			static::$_instance = new static();
		}
		return static::$_instance;
	}

	function setVersion($version) {
		$this->_version = $version;
		return $this;
	}

	function addCss($srcOrCode, $section=null, $version=true, $media=null) {
		if (is_null($section)) {
			$section = 'head';
		}
		elseif (!array_key_exists($section, $this->_css)) {
			throw new \InvalidArgumentException();
		}

		if (preg_match(self::RAW_CSS_PATTERN, $srcOrCode)) {
			if (substr($srcOrCode, -1) != "\n") {
				$srcOrCode.= "\n";
			}
			$this->_css[$section].= $srcOrCode;
		}
		else {
			$this->_css['files'][] = array(
					'href' => $srcOrCode,
					'version' => $version,
					'versionString' => static::_getVersionString($version),
					'media' => $media,
			);
		}
		return $this;
	}
	function addJs($srcOrCode, $section=null, $version=true, $async=null) {

		if (is_null($section)) {
			$section = 'head';
		}
		elseif (!array_key_exists($section, $this->_js)) {
			throw new \InvalidArgumentException();
		}

		// if it's raw js code:
		if (preg_match(self::RAW_JS_PATTERN, $srcOrCode)) {
			// ensure newline at the end
			if (substr($srcOrCode, -1) != "\n") {
				$srcOrCode.= "\n";
			}
			// @todo do indent minimizing here ! ?
			$this->_js[$section].= $srcOrCode;
		}
		else {
			$this->_js['files'][] = array(
					'src' => $srcOrCode,
					'version' => $version,
					'versionString' => static::_getVersionString($version),
					'async' => $async,
			);
		}
		return $this;
	}

	function render($section) {
		switch($section) {
			case 'head':
				$arrayFilter = array_flip(array('head', 'onLoad'));
				$View = \View::build('Assets/head.html');
				$View->assign(array(
						'jsFiles' => $this->_js['files'],
						'jsCodes' => array_intersect_key($arrayFilter, $this->_js),
						'cssFiles' => $this->_css['files'],
						'cssHead' => $this->_css['head'],
				));
				$ret = $View;

				break;
			case 'bodyEnd':
				break;
			default:
				throw \InvalidArgumentException();
		};
		return $ret;
	}

	/**
	 * I return a <script ...></script> tag
	 * @param string $fileName
	 * @param string $version self::_getVersionString($version) will be called
	 * @return string
	 */
	public static function getJsInclude($fileName, $version=null) {
		$ret = '<script' .
			' type="text/javascript"' .
			' src="' . $fileName . (self::_getVersionString($version)) . '"' .
			'></script>';
		return $ret;
	}

	/**
	 * I returna <link ... /> tag
	 * @param string $fileName should be full fname, no further processing is done
	 * @param string $media if set, will be used
	 * @param string $version self::_getVersionString($version) will be called
	 * @return string
	 */
	public static function getCssInclude($fileName, $media=null, $version=null) {
		$ret = '<link' .
			' rel="stylesheet"' .
			' href="' . $fileName . self::_getVersionString($version) . '"' .
			(is_null($media) ? '' : ' media="' . $media . '"') .
			' />';
		return $ret;
	}

	/**
	 * I get a version string.
	 * @param true|string|boolean|null $version true to return static version var, string or int to make version q string
	 * @return string
	 */
	protected function _getVersionString($version) {
		if ($version === true) {
			return self::_getVersionString($this->_version);
		}
		elseif (is_string($version) || is_integer($version)) {
			$ret = '?' . $version;
		}
		else {
			$ret = '';
		}
		return $ret;
	}

}
