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
 *  note annotation of command line params. These phpDoc comments will be available in "shake.php mix classlist help"
 * 	format is:
 * 		the param name without '_param' prefix is considered to be available as command line --paramname
 * 		any line beginning with a dash followed by a word is considered an alias for the param
 * 		a @var string will imply that the param needs a value as well
 * 		first line of comment is description
 * 	eg. for $_paramStartPath the generated help will be:
 * 		-i [--startpath] <value>   path to start searching for src
 */

/**
 * Classlist tool
 * @author t
 * @package Cocktail\Controller
 * @version 1.01
 */
class ShakeMixClasslist extends \ShakeMix {

	/**
	 * path to start searching for src
	 * -i
	 * @var string
	 */
	protected $_paramStartPath = 'src';

	/**
	 * path to filename (single file mode) or folder in which in which I write class alias definitions
	 * -o
	 * @var string
	 */
	protected $_paramOutPath = 'classlist';

	/**
	 * if specified, one file per class alias will be written (default is one file with all aliases)
	 * -m
	 * @var bool
	 */
	protected $_paramManyFiles = false;

	/**
	 * file/path masks to be skipped, PLANNED
	 * -s
	 * @var string[]
	 * @todo implement skipMask
	 */
	protected $_paramSkipMask = array();

	/**
	 * if specified, I check consistency of src PLANNED
	 * -wc
	 * @var bool
	 * @todo implement withCheck, call another Shake tool to do so
	 */
	protected $_withCheck = false;

	/**
	 * @var resource static file pointer used when in single file mode
	 */
	protected $_fp;

	/**
	 * @var array[] I gather class definitions in this var
	 */
	protected $_classes = array();

	/**
	 * I validate skipmask and set it
	 * @param string $skipMask
	 * @return $this
	 * @throws \RuntimeException
	 * @throws \UnimplementedException
	 */
	public function _setParamSkipMask($skipMask=null) {
		if (is_null($skipMask)) {
			throw new \RuntimeException('skipMask is empty');
		}
		if (!empty($skipMask)) {
			// @todo check format here before setting
			$this->_paramSkipMask = explode(',', $skipMask);
			throw new \UnimplementedException;
		}
		else {
			$this->_paramSkipMask = array();
		}
		return $this;
	}

	/**
	 * I open _ationHelp() to generate automatic help
	 * @return mixed
	 */
	public function actionAllHelp() {
		return parent::_actionAllHelp();
	}

	/**
	 * I generate the class list file(s)
	 * @return string|void
	 * @throws \RuntimeException
	 * @throws \UnimplementedException
	 */
	public function actionIndex() {

		if (!$this->_paramManyFiles && !preg_match('|\.php$|', $this->_paramOutPath)) {
			$this->_paramOutPath.= '.php';
		}

		if (!is_dir($this->_paramStartPath) || !is_readable($this->_paramStartPath)) {
			throw new \RuntimeException('startPath does not exist or is not writable');
		}
		if ($this->_paramManyFiles &&
				(!is_dir($this->_paramOutPath) || !is_writable($this->_paramOutPath))) {
			if (!$this->_paramTestMode) {
				throw new \RuntimeException('outPath does not exist or is not writable');
			}
		}
		elseif (!$this->_paramManyFiles &&
				(is_dir($this->_paramOutPath))) {
			if (!$this->_paramTestMode) {
				throw new \RuntimeException('outPath is not writable');
			}
		}

		// try to open outfile before processing, and don't proceed if open fails
		if (!$this->_paramManyFiles) {
			if (!$this->_paramTestMode) {
				if (file_exists($this->_paramOutPath) && !$this->_paramOverWrite) {
					throw new \RuntimeException('outPath file exists, use --overwrite or -f to overwrite');
				}
				$this->_fp = fopen($this->_paramOutPath, 'w+');
				if (!$this->_fp) {
					throw new \RuntimeException('outPath file could not be opened for writing');
				}
			}
		}

		// process input files
		$this->_processDir($this->_paramStartPath);

		if ($this->_paramManyFiles) {
			$this->_writeManyFiles();
		}
		else {
			$this->_writeOneFile();
		}

		if ($this->_fp) {
			fclose ($this->_fp);
		}

		$footer = \View::build('Shake/footer');
		$this->_Response->addContent($footer);

		die;
	}

	/**
	 * I iterate a folder, find subfolders and files, then process first subfolders, then files
	 * @param string $dir
	 */
	protected function _processDir($dir) {
		\Camarera::log(\Camarera::LOG_INFORMATIONAL, 'processing dir: ' . $dir);
		if (!is_readable($dir)) {
			\Camarera::log(\Camarera::LOG_INFORMATIONAL, 'cannot read directory: ' . $dir);
			return;
		}
		$dir = dir($dir);
		$filesToProcess = $dirsToProcess = array();
		while ($eachDir = $dir->read()) {
			if (in_array($eachDir, array('.', '..')));
			elseif (is_dir($d = $dir->path . '/' . $eachDir)) {
				$dirsToProcess[] = $d;
			}
			elseif (is_file($f = $dir->path . '/' . $eachDir)) {
				$filesToProcess[] = $f;
			}
			else {
				echop($eachDir);
				die('WTF');
			}
		}
		foreach ($dirsToProcess as $eachDir) {
			$this->_processDir($eachDir);
		}
		foreach ($filesToProcess as $eachFile) {
			$this->_processFile($eachFile);
		}

	}

	/**
	 * I decide if a file should be processed or not
	 * @param string $fname filename with path
	 * @return bool true if should be processed
	 */
	protected function _isProcessableFile($fname) {
		if (preg_match('|\.class\.php$|', $fname)) {
			return true;
		}
		// @todo implement exclude checking here
		return false;
	}

	/**
	 * I check if file is readable then tokenize it and find class definitions. Add them to $this->_classes
	 * @param string $fname path to a file to process
	 */
	protected function _processFile($fname) {
		if (!$this->_isProcessableFile($fname)) {
			\Camarera::log(\Camarera::LOG_INFORMATIONAL, 'skipping: ' . $fname);
			return;
		}
		\Camarera::log(\Camarera::LOG_INFORMATIONAL, 'processing: ' . $fname);
		if (!is_readable($fname)) {
			\Camarera::log(\Camarera::LOG_INFORMATIONAL, '   cannot read file: ' . $fname);
			return;
		}
		$f = file_get_contents($fname);
		$tokens = token_get_all($f);
		$tokens = array_filter($tokens, function($token) {
			if (is_array($token) && $token[0] == T_WHITESPACE) {
				return false;
			}
			return true;
		});
		$tokens = array_merge($tokens);

		$maxI = count($tokens) - 1;
		$namespace = $classname = '';
		for ($i=0; $i<$maxI; $i++) {
			if (($tokens[$i][0] == T_NAMESPACE) && ($tokens[$i+1][0] == T_STRING)) {
				$namespace = $tokens[$i+1][1];
				$i++;
			}
			elseif (($tokens[$i][0] == T_CLASS) && ($tokens[$i+1][0] == T_STRING)) {
				// @todo here I could get phpdoc as well
				$isAbstract = is_array($tokens[$i-1]) && ($tokens[$i-1][0] == T_ABSTRACT) ? true : false;
				if ($tokens[$i-($isAbstract?2:1)][0] == T_DOC_COMMENT) {
					$docComment = $tokens[$i-($isAbstract?2:1)][1];
				}
				else {
					$docComment = '/**' . "\n" .
						' * root namespace alias for IDEs ' . "\n" .
						' */';
				}
				$this->_classes[] = array(
					'abstract' => $isAbstract,
					'namespace' => $namespace,
					'classname' => $tokens[$i+1][1],
					'comment' => $docComment,
				);
				\Camarera::log(\Camarera::LOG_INFORMATIONAL, '   found: ' . $namespace . '\\' . $tokens[$i+1][1]);
			}
		}
	}

	/**
	 * I write one file per class
	 * @param array[] $classes
	 * @throws \UnimplementedException
	 */
	protected function _writeManyFiles() {
		$Header = \View::build('Shake/Mix/Classlist/header');
		$header = $Header->render();
		$Alias = \View::build('Shake/Mix/Classlist/alias');
		foreach ($this->_classes as $eachClass) {
			$fname = $this->_paramOutPath . '/' . $eachClass['classname'] . '.php';
			if ($this->_paramTestMode) {
				$this->_Response
					->addContent('============================================================'."\n")
					->addContent('FILE: ' . $fname."\n")
					->addContent('============================================================'."\n")
					->addContent($header)
					->addContent($Alias->render($eachClass));
			}
			else {
				if (file_exists($fname) && !$this->_paramOverWrite) {
					\Camarera::log(\Camarera::LOG_INFORMATIONAL, 'SKIPPING WRITE, file exists: ' . $fname);
					continue;
				}
				$fp = fopen($fname, 'w+');
				fwrite($fp, $header);
				fwrite($fp, $Alias->render($eachClass));
				fclose($fp);
				\Camarera::log(\Camarera::LOG_INFORMATIONAL, 'written: ' . $fname);
			}
		}
	}

	/**
	 * I write one file with all class alias commands
	 * @param array[] $classes
	 */
	protected function _writeOneFile() {
		$Header = \View::build('Shake/Mix/Classlist/header');
		$header = $Header->render();
		$Alias = \View::build('Shake/Mix/Classlist/alias');

		if ($this->_paramTestMode) {
			$this->_Response
				->addContent('============================================================'."\n")
				->addContent('FILE: ' . $this->_paramOutPath . "\n")
				->addContent('============================================================'."\n")
				->addContent($header);
			foreach ($this->_classes as $eachClass) {
				$this->_Response->addContent($Alias->render($eachClass));
			}
		}
		else {
			fwrite($this->_fp, $header);
			foreach ($this->_classes as $eachClass) {
				fwrite($this->_fp, $Alias->render($eachClass));
			}
			\Camarera::log(\Camarera::LOG_INFORMATIONAL, 'written outfile');
		}
	}
}
