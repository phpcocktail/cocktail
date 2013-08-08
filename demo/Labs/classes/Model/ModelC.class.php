<?php

class ModelCHelper {

	public static function inflate($fields, $classmame, $fieldNamePattern, $idFieldName) {

		// get field defs
		if (empty($fields) || !is_array($fields)) {
			throw new \ClassDefinitionException('initial field definition array is empty');
		}
		// inflate field objects
		// @todo abstract to FieldHelper class ! ?
		foreach ($fields as $eachFieldName=>&$eachField) {

			if (!preg_match($fieldNamePattern, $eachFieldName)) {
				throw new \ClassDefinitionException(
						'field name ' . $eachFieldName . ' does not match field name pattern: ' . static::FIELD_NAME_PATTERN
				);
			}

			// $eachField may already be a FieldXxx object, otherwise must be an array
			if (is_object($eachField) && is_subclass_of($eachField, 'Camarera\Field'));
			elseif (is_array($eachField)) {

				if (!empty($eachField['type'])) {
					$classname = '\Field' . ucFirst($eachField['type']);
				}
				elseif (!empty($eachField['classname'])) {
					$classname = $eachField['classname'];
				}
				else {
					throw new \ClassDefinitionException(
							'neither "classname" nor "type" is set in ' . get_called_class() . ' field ' . $eachFieldName
					);
				};

				try {
					if (!class_exists($classname)) {
						throw new \AutoloaderException();
					}
				}
				catch (AutoloaderException $e) {
					throw new \ClassDefinitionException(
							'class ' . $classname . ' not found'
					);
				}

				$eachField = $classname::get($eachField, $eachFieldName, get_called_class());

			}
			// allow shorthand declarations of just fieldname instead of config array
			elseif (is_string($eachField) && is_numeric($eachFieldName)) {
				$fields[$eachField] = \FieldString::get(array('type'=>'string'), $eachField.'x', get_called_class());
				unset($fields[$eachFieldName]);
			}
			else {
				throw new \ClassDefinitionException('invalid field def in ' . get_called_class() . ' field ' . $eachFieldName);
			}

		}

		// if ID field is a single field, and missing, add it
		if (is_string($idFieldName) && !array_key_exists($idFieldName, $fields)) {
			$fields = array_reverse($fields, true);
			$fieldConfig = array(
					'type' => 'integer',
			);
			$classname = \Camarera::conf('Field.id.class');
			$Field = $classname::get($fieldConfig, $idFieldName, get_called_class());
			$fields[$idFieldName] = $Field;
			$fields = array_reverse($fields, true);
		}
		// else check if all ID fields exist
		elseif (is_array($idFieldName)) {
			$missingFields = array();
			foreach ($idFieldName AS $eachIdFieldName) {
				if (!array_key_exists($eachIdFieldName, $fields)) {
					$missingFields[] = $eachIdFieldName;
				}
			}
			if (!empty($missingFields)) {
				throw new \ClassDefinitionException(
						'fields ' . implode(',', $missingFields) . ' are ID fields but not defined in ' . get_called_class()
				);
			}
		}

		return $fields;

	}

}

/**
 * An exact copy of Camarera\Model as of version 1.01 for testing
 */
abstract class ModelC {

	protected static function _inflate() {
		// init only once. Keep data in self array instead of statics, since otherwise misdefined src with missing
		//		static $_inflated definition sometimes would not get initialized at all
		$classname = get_called_class();
		if (isset(self::$_inflated[$classname]) && (self::$_inflated[$classname]=== true)) {
			return;
		}
		// check if mandatory properties are defined
		foreach (self::$_mandatoryProperties as $eachProperty) {
			try {
				$rProperty = new \ReflectionProperty($classname, $eachProperty);
				if ($rProperty->getDeclaringClass()->name !== $classname) {
					unset($rProperty);
					throw new \Exception();
				}
				unset($rProperty);
			}
			catch (\Exception $e) {
				throw new \ClassDefinitionException('static::$' . $eachProperty . ' property not declared in ' . get_called_class());
			}
		}

		if (empty(static::$_idFieldName)) {
			static::$_idFieldName = \Camarera::conf('Field.id.name');
		}

		static::$_fields = ModelCHelper::inflate(
				static::_getInitialFieldDefs(),
				$classname,
				static::FIELD_NAME_PATTERN,
				static::$_idFieldName
		);

		if (is_null(static::$_storeTable)) {
			$storeTable = get_called_class();
			if ($pos = strrpos($storeTable, '\\')) {
				$storeTable = substr($storeTable, $pos+1);
			}
			static::$_storeTable = strtolower($storeTable);
		}

		self::$_inflated[get_called_class()] = true;


	}

	/** @var string FIELD_NAME_PATTERN this is the pattern to match property names */
	const FIELD_NAME_PATTERN = '/^(\_id)|([a-z]+[a-zA-Z0-9])*$/';

	/**
	 * @var string[] these fields must be re-declared in all subclasses locally
	 */
	private static $_mandatoryProperties = array(
		'_idFieldName',
		'_fields',
		'_storeTable',
	);

	/** @var boolean $_inflated I set true here once static inflation is done */
	private static $_inflated = array();

	/** @var \Field[] field objects */
	protected static $_fields = null;
	/** @var string[]|string I cache idFieldnames here, can be single string or array */
	protected static $_idFieldName = null;
	/** @var string I use this to implode id field values into uniqe ID, if there are more than one id fields */
	protected static $_idFieldGlue = '_';

	//////////////////////////////////////////////////////////////////////////
	// STATIC
	//////////////////////////////////////////////////////////////////////////

	/**
	 * this will be called by _inflate() to get raw field defs. You can override this to return field defs
	 *	dynamicly (if simple class array definition in self::$_fields is not enough)
	 * @return array
	 */
	protected static function _getInitialFieldDefs() {
		return static::$_fields;
	}

	/**
	 * I get one or all fields
	 * @param null|string|string[] $fieldName null = get all fields, string = get one field, array = get some fields
	 * @return array|\Field
	 * @throws \InvalidArgumentException
	 */
	public static function getField($fieldNames=null) {
		static::_inflate();
		if (is_null($fieldNames)) {
			return static::$_fields;
		}
		elseif (is_array($fieldNames)) {
			return array_intersect_key(static::$_fields, array_flip($fieldNames));
		}
		elseif (is_string($fieldNames) && array_key_exists($fieldNames, static::$_fields)) {
			return static::$_fields[$fieldNames];
		}
		else {
			throw new \InvalidArgumentException('field ' . print_r($fieldNames,1) . ' not found in ' . get_called_class());
		}
	}

	//////////////////////////////////////////////////////////////////////////
	// INSTANCE MANAGEMENT
	//////////////////////////////////////////////////////////////////////////

	/**
	 * @var \ModelGetConfig the last Config used in a get() or load()
	 */
	protected $_lastGetConfig = null;

	/**
	 * I return an instance
	 * @param null|int|string|array|ModelGetConfig $config depending on $config I will return various results
	 * 		null - empty object
	 * 		int|string - object with that ID @see setId()
	 * 		array - key=>value pairs with which returned object will be initialized with
	 * 		ModelGetConfig - same as calling with (null, $Config)
	 * @param ModelGetConfig $Config get options. @see ModelGetConfig for options
	 * @return \Model
	 */
	public static function get($dataOrConfig=null, \ModelGetConfig $Config=null) {

		if (!is_null($Config)) {
			$Config->data = $dataOrConfig;
			return static::get($Config);
		}

		if (is_null($dataOrConfig)) {
			$Config = \ModelGetConfig::get();
		}
		elseif (is_string($dataOrConfig) || is_integer($dataOrConfig) || is_array($dataOrConfig)) {
			$Config = \ModelGetConfig::get(array(
					'data' => $dataOrConfig,
			));
		}
		elseif (is_object($dataOrConfig) && ($dataOrConfig instanceof Camarera\ModelGetConfig)) {
			$Config = $dataOrConfig;
		}
		else {
			throw new \InvalidArgumentException();
		}

		if (!isset($Config->data) || is_null($Config->data)) {
			$Model = new static();
		}
		elseif (!empty($Config->data) &&
				static::$_isManageable &&
				$Config->managedInstance &&
				$Config->allowLoad &&
				($Model = \ModelManager::get(get_called_class(), $Config->data)));
		elseif (is_integer($Config->data) || is_string($Config->data)) {
			$Model = new static();
			$Model->setId($Config->data);
			if ($Config->allowLoad) {
				$result = $Model->load($Config);
				if ($result && $Config->managedInstance) {
					$Model->registerInstance();
				}
			}
		}
		elseif (is_array($Config->data)) {
			$Model = new static();
			foreach ($Config->data AS $eachField=>$eachValue) {
				$Model->setValue($eachField, $eachValue);
			}
			if ($Config->allowLoad) {
				$result = $Model->load($Config);
				if ($result && $Config->managedInstance) {
					$Model->registerInstance();
				}
			}
		}

		$Model->_lastGetConfig = $Config;

		return $Model;

	}


	//////////////////////////////////////////////////////////////////////////
	// CONSTRUCT, MAGIC
	//////////////////////////////////////////////////////////////////////////

	protected function __construct() {
		static::_inflate();
	}

	public function __get($field) {
		switch(true) {
			case $field === 'ID':
				return $this->getID();
			case $field === 'idFieldName':
				return static::getIdFieldName();
			case $field === 'isDirty':
				return $this->isDirty();
			case $field === 'isManageable':
				return static::$_isManageable;
			case $field === 'isRegistered':
				return $this->_isRegistered;
			case $field === 'storeTable':
				return $this->getStoreTable();
			//case $field === 'isLoaded':
			// magic field value getters
			case (preg_match(static::FIELD_NAME_PATTERN, $field)):
				return $this->getValue($field);
			default:
				throw new \MagicGetException($field, get_class($this));
		}
	}
	public function __set($field, $value) {
		switch (true) {
			case $field === 'ID':
				$this->setID($value);
				break;
			case preg_match(static::FIELD_NAME_PATTERN, $field):
				$this->setValue($field, $value);
				break;
			default:
				throw new \MagicSetException($field, get_class($this));
		}
	}


	//////////////////////////////////////////////////////////////////////////
	// DATA
	//////////////////////////////////////////////////////////////////////////

	protected $_values = array();
	protected $_storedValues = array();

	/**
	 * I return (actual) ID value
	 * @return string
	 */
	public function getID() {
		$id = null;
		$idFieldName = $this->getIdFieldName();
		if (is_string($idFieldName)) {
			$id = $this->getValue($idFieldName);
		}
		elseif (is_array($idFieldName)) {
			$idFields = array();
			foreach ($idFieldName as $eachIdFieldName) {
				$idFields[] = $this->getValue($eachIdFieldName);
			}
			$id = implode(static::$_idFieldGlue, $idFields);
		}
		return $id;
	}
	/**
	 * static version of getID()
	 * @param array $data
	 * @return string the ID in string or null
	 */
	public static function calculateIdByArray(array $data) {
		$id = null;
		$idFieldName = static::getIdFieldName();
		if (is_string($idFieldName)) {
			$id = array_key_exists($idFieldName, $data) ? $data[$idFieldName] : null;
		}
		elseif (is_array($idFieldName)) {
			$idFields = array();
			foreach ($idFieldName as $eachIdFieldName) {
				$idFields[] = array_key_exists($eachIdFieldName, $data) ? $data[$idFieldName] : null;
			}
			$id = implode(static::$_idFieldGlue, $idFields);
		}
		return $id;
	}
	/**
	 * I try to set ID field values. I can only set if there is just one ID field,
	 * @param string $id
	 * @throws \RuntimeException
	 * @throws \BadMethodCallException
	 * @return \Model
	 */
	public function setID($id) {

		if (is_array($id)) {

			foreach ($id as $eachKey=>$eachValue) {
				$this->setValue($eachKey, $eachValue);
			}

		}
		elseif (is_string($id) || is_integer($id)) {

			if (empty(static::$_idFieldGlue)) {
				throw new \RuntimeException('static::$_idFieldGlue not defined in ' . get_called_class());
			}
			// @todo this should be examined based on the type(s) of id field(s)
			elseif (!is_string($id) && !is_integer($id)) {
				throw new \BadMethodCallException('id ' . print_r($id,1) . ' invalid');
			}

			$idFieldName = static::getIdFieldName();
			if (is_string($idFieldName)) {
				$this->setValue($idFieldName, $this->getField($idFieldName)->setValue($id));
			}
			// @todo test this
			elseif (is_array($idFieldName)) {
				if ((substr_count($id, static::$_idFieldGlue)+1) !== count($idFieldName)) {
					throw new \BadMethodCallException('id ' . $id . ' invalid');
				}
				$idParts = explode(static::$_idFieldGlue, $id);
				foreach ($idFieldName as $eachKey => $eachIdFieldName) {
					$idPart = $idParts[$eachKey];
					$idPart = $this->getField($eachIdFieldName)->setValue($idPart);
					$this->setValue($eachIdFieldName, $idPart);
				}
			}
		}
		else {
			throw new \BadMethodCallException('$id should be string or array');
		}

		return $this;
	}

	/**
	 * I get one value, if is readable (otherwise, you have to take care of getting that value by a getter)
	 * @todo implement is_readable
	 * @param null|string|array $field field name or names to get, null to return all
	 * @throws \MagicGetException
	 * @return null|array|mixed field or fields based on param $field
	 */
	public function getValue($field, $storedValue=false) {

		if (is_null($field)) {
			$field = array_keys($storedValue ? $this->_storedValues : $this->_values);
		}

		if (is_array($field)) {
			$ret = array();
			foreach ($field as $eachField) try {
				$ret[$eachField] = $this->getValue($eachField, $storedValue);
			}
			catch (\Exception $e) {};
		}
		elseif (is_string($field)) {
			if (!array_key_exists($field, static::$_fields)) {
				throw new MagicGetException($field, get_class($this));
			}
			$ret = null;
			if (array_key_exists($field, $this->_values)) {
				// I call the field get filter/processor
				$ret = $this->getField($field)->getValue($storedValue ? $this->_storedValues[$field] : $this->_values[$field]);
			}
		}
		else {
			throw new \BadMethodCallException('invalid parameter for getValue, only string and array are valid');
		}
		return $ret;
	}
	/**
	 * set one value, if it is writeable (otherwise, you have to take care of setting that parameter)
	 * @todo implement is_writable
	 * @param unknown $field
	 * @param unknown $value
	 * @throws MagicSetException
	 * @return \Model
	 */
	public function setValue($field, $value) {
		if (!array_key_exists($field, static::$_fields)) {
			throw new \MagicSetException($field, get_class($this));
		}

		// I call field object's set validator/processor. Normally it returns $value unintact, otherwise modifies it
		$this->getField($field)->setValue($value);
		$this->_values[$field] = $value;
		return $this;
	}
	/**
	 * I apply an array of field=>value pairs
	 * @param array $values field=>value pairs
	 * @param boolean $replace if true, I replace current data, otherwise append
	 * @param boolean $throw if true, errors will be thrown (field not found, etc), otherwise just apply as can
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 * @return \Model
	 */
	public function setValues($values, $replace=false, $throw=true) {
		if (!is_array($values)) {
			throw new \InvalidArgumentException("setValues argument not array");
		}
		if ($replace) {
			$this->_values = array();
		}
		foreach ($values as $eachFieldName=>&$eachValue) try {
			$this->setValue($eachFieldName, $eachValue);
		}
		catch (\Exception $e) {
			if ($throw) {
				throw $e;
			}
		}
		return $this;
	}
	/**
	 * I store current values as the stored ones
	 * @return \Model
	 */
	public function setStoredValues() {
		$this->_storedValues = $this->_values;
		return $this;
	}

	/**
	 * check if I am dirty (at least field value have been changed since creation/last save/load
	 * @return boolean true if I am dirty
	 */
	public function isDirty() {
		return $this->_values === $this->_storedValues ? true : false;
	}
	public function isFieldDirty($field) {
		if (is_array($field)) {
			$ret = $field;
			foreach ($ret AS &$eachField) {
				$eachField = $this->isFieldDirty($eachField);
			}
		}
		elseif (is_string($field)) {
			$ret = $array_key_exists($eachField, $this->_values) &&
					array_key_exists($eachField, $this->_storedValues) &&
					($this->_values[$eachField] == $this->_storedValues[$eachField])
				? true : false;
		}
		else {
			throw new \BadMethodCallException('field name invalid');
		}
		return $ret;
	}

	/**
	 * simle array functions which tells if current values contain the given dataset or not
	 * @param data $data as in $this->_values, must have the proper keys
	 * @return boolean
	 */
	function valuesContain($data) {
		return count(array_diff_assoc($data, $this->_values)) ? false : true;
	}


	//////////////////////////////////////////////////////////////////////////
	// VALIDATION
	//////////////////////////////////////////////////////////////////////////

	public function validate() {
		echop($this);
	}

}
