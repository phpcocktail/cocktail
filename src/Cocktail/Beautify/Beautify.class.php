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
 * I am used to produce nicer output by eg. indenting blocks properly or compacting. Set
 * @author t
 * @package Cocktail\Beautify
 * @version 1.01
 */
class Beautify {

	/**
	 * @var int content returned as-is. Being the fasted, this is recommended for production
	 */
	const MODE_NONE = 0;
	/**
	 * @var int somewhat compacts the output, however, may be necessary for readability, othervise deprecated. Breaks
	 * 		text inside <pre> as well.
	 */
	const MODE_COMPACT = 1;
	/**
	 * @var int adjust indenting to the specified level, remove unnecessary newlines
	 */
	const MODE_BEAUTIFUL = 2;
	/**
	 * @var int as MODE_BEAUTIFUL but ensures there is a newline at the beginning and at the end of the content. Usually
	 * 		this mode ensure maximum readability
	 */
	const MODE_BEAUTIFUL_NEWLINES = 128;

	/**
	 * @var int as in constants, will be fetched from config .Beautify.mode if is null and getMode() is called
	 */
	protected static $_mode = null;

	/**
	 * I return current mode. If null, set it from conf
	 * @return int
	 */
	static public function getMode() {
		if (is_null(self::$_mode)) {
			self::$_mode = \Camarera::conf('Beautify.mode');
		}
		return self::$_mode;
	}
	/**
	 * I set global mode
	 * @param int $mode
	 * @throws \InvalidArgumentException
	 */
	static public function setMode($mode) {
		if (!in_array($mode, array(0, 1, 2, 128, 129, 130), true)) {
			throw new \InvalidArgumentException();
		};
		static::$_mode = $mode;
	}

	/**
	 * I am a shortcut to beauty()
	 * @param mixed $content
	 * @param int $tabCount
	 * @param int $mode
	 * @see beauty()
	 */
	static function b($content, $tabCount=0, $mode=null) {
		return static::beauty($content, $tabCount, $mode);
	}

	/**
	 * I beautify content. Note content will be casted to string unless $mode is MODE_NONE
	 * @param mixed $content anything that can be echo'ed (string, number, object with __toString() method)
	 * @param int $tabCount adjust main tabbing to this level
	 * @param int $mode mode to use, null to use global setting
	 * @return string the processed string
	 */
	static function beauty($content, $tabCount=0, $mode=null) {

		if (is_null($mode)) {
			$mode = static::$_mode;
		}

		if ($mode == self::MODE_NONE) {
			return $content;
		}

		$content = \Util::toString($content);

		switch ($mode) {
			case self::MODE_BEAUTIFUL_NEWLINES:
			case self::MODE_BEAUTIFUL:
				$ensureNewlines = $mode == self::MODE_BEAUTIFUL_NEWLINES;
				// remove base indent level, first find out how many tabs to remove
				if (preg_match('/^\t+/', $content, $matches)) {
					$inTabCount = strlen($matches[0]);
					for ($offset=0; $offset=strpos($content, "\n", $offset+1); ) {
						// continue on empty line
						if (!isset($content[$offset+1])) {
							break;
						}
						elseif ($content[$offset+1]=="\n") {
							continue;
						}
						for ($i=$offset; ($i==$offset) || (($i<strlen($content)) && ($content[$i]== "\t")); $i++);
						$inTabCount = min($inTabCount, $i - $offset-1);
					}
					$content = str_replace("\n" . str_repeat("\t", $inTabCount), "\n", $content);
					$content = substr($content, $inTabCount);
				}

				// get rid of double newlines
				$content = preg_replace('/\n(\s)*\n/', "\n", $content);

				// now do the indenting, it is attached to ensure newlines
				// ensure beginning newline if necessary
				if ($ensureNewlines && (substr($content, 0) != "\n")) {
					$content = "\n" . $content;
				}
				// do actual indenting
				if ($tabCount) {
					$content = str_replace("\n", "\n" . str_repeat("\t", $tabCount), $content);
 					if (substr($content, -($tabCount+1)) == "\n" . str_repeat("\t", $tabCount)) {
						$content = substr($content, 0, -$tabCount);
					}
				}
				// ensure ending newline if necessary
				if ($ensureNewlines && (substr($content, -1) != "\n")) {
					$content.= "\n";
				};

				break;
			case self::MODE_COMPACT:
				$content = str_replace("\t", " ", $content);
				while (strpos($content, "  ") !== false) {
					$content = str_replace("  ", " ", $content);
				}
				$content = str_replace("\n ", "\n", $content);
				while (strpos($content, "\n\n") !== false) {
					$content = str_replace("\n\n", "\n", $content);
				}
				break;
			case self::MODE_NONE:
			default:
				break;
		}
		return $content;
	}

	/**
	 * I strip of head comment of php file
	 * @param $content
	 * @param string $mode
	 * @return mixed
	 */
	static function stripHeadComment($content, $mode='php') {
		if (preg_match('/^(<\?(php)?\s*\/\*\*.+?\*\/)(.+)$/s', $content, $matches)) {
			return ltrim($matches[3]);
		}
		return $content;
	}

}
