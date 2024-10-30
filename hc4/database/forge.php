<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Database_Forge
{
	/**
	 * Fields data
	 *
	 * @var	array
	 */
	public $fields		= array();

	/**
	 * Keys data
	 *
	 * @var	array
	 */
	public $keys		= array();

	/**
	 * Primary Keys data
	 *
	 * @var	array
	 */
	public $primary_keys	= array();

	/**
	 * Database character set
	 *
	 * @var	string
	 */
	public $db_char_set	= '';

	/**
	 * DROP DATABASE statement
	 *
	 * @var	string
	 */
	protected $_drop_database	= 'DROP DATABASE %s';

	/**
	 * CREATE TABLE statement
	 *
	 * @var	string
	 */
	protected $_create_table	= "%s %s (%s\n)";

	/**
	 * CREATE TABLE IF statement
	 *
	 * @var	string
	 */
	protected $_create_table_if	= 'CREATE TABLE IF NOT EXISTS';

	/**
	 * DROP TABLE IF EXISTS statement
	 *
	 * @var	string
	 */
	protected $_drop_table_if	= 'DROP TABLE IF EXISTS';

	/**
	 * RENAME TABLE statement
	 *
	 * @var	string
	 */
	protected $_rename_table	= 'ALTER TABLE %s RENAME TO %s;';

	/**
	 * DEFAULT value representation in CREATE/ALTER TABLE statements
	 *
	 * @var	string
	 */
	protected $_default		= ' DEFAULT ';

	/**
	 * CREATE DATABASE statement
	 *
	 * @var	string
	 */
	protected $_create_database	= 'CREATE DATABASE %s CHARACTER SET %s COLLATE %s';

	/**
	 * CREATE TABLE keys flag
	 *
	 * Whether table keys are created from within the
	 * CREATE TABLE statement.
	 *
	 * @var	bool
	 */
	protected $_create_table_keys	= TRUE;

	/**
	 * UNSIGNED support
	 *
	 * @var	array
	 */
	protected $_unsigned		= array(
		'TINYINT',
		'SMALLINT',
		'MEDIUMINT',
		'INT',
		'INTEGER',
		'BIGINT',
		'REAL',
		'DOUBLE',
		'DOUBLE PRECISION',
		'FLOAT',
		'DECIMAL',
		'NUMERIC'
	);

	/**
	 * NULL value representation in CREATE/ALTER TABLE statements
	 *
	 * @var	string
	 */
	protected $_null = 'NULL';

	public function __construct(
		HC4_Database_Interface $db,
		HC4_Database_QueryBuilder $qb
		)
	{
		$this->db = $db;
		$this->qb = $qb;
	}

	// --------------------------------------------------------------------

	/**
	 * Create database
	 *
	 * @param	string	$db_name
	 * @return	bool
	 */
	public function create_database($db_name)
	{
		if ($this->_create_database === FALSE)
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unsupported_feature') : FALSE;
		}
		elseif ( ! $this->db->query(sprintf($this->_create_database, $db_name, $this->db->char_set, $this->db->dbcollat)))
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unable_to_drop') : FALSE;
		}

		if ( ! empty($this->db->data_cache['db_names']))
		{
			$this->db->data_cache['db_names'][] = $db_name;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @param	string	$db_name
	 * @return	bool
	 */
	public function drop_database($db_name)
	{
		if ($this->_drop_database === FALSE)
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unsupported_feature') : FALSE;
		}
		elseif ( ! $this->db->query(sprintf($this->_drop_database, $db_name)))
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unable_to_drop') : FALSE;
		}

		if ( ! empty($this->db->data_cache['db_names']))
		{
			$key = array_search(strtolower($db_name), array_map('strtolower', $this->db->data_cache['db_names']), TRUE);
			if ($key !== FALSE)
			{
				unset($this->db->data_cache['db_names'][$key]);
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Add Key
	 *
	 * @param	string	$key
	 * @param	bool	$primary
	 * @return	CI_DB_forge
	 */
	public function add_key($key, $primary = FALSE)
	{
		// DO NOT change this! This condition is only applicable
		// for PRIMARY keys because you can only have one such,
		// and therefore all fields you add to it will be included
		// in the same, composite PRIMARY KEY.
		//
		// It's not the same for regular indexes.
		if ($primary === TRUE && is_array($key))
		{
			foreach ($key as $one)
			{
				$this->add_key($one, $primary);
			}

			return $this;
		}

		if ($primary === TRUE)
		{
			$this->primary_keys[] = $key;
		}
		else
		{
			$this->keys[] = $key;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add Field
	 *
	 * @param	array	$field
	 * @return	CI_DB_forge
	 */
	public function add_field( $field )
	{
		if (is_string($field)){
			if ($field === 'id'){
				$this->add_field(array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 9,
						'auto_increment' => TRUE
					)
				));
				$this->add_key('id', TRUE);
			}
			else {
				if (strpos($field, ' ') === FALSE){
					show_error('Field information is required for that operation.');
				}

				$this->fields[] = $field;
			}
		}

		if (is_array($field)){
			$this->fields = array_merge($this->fields, $field);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Create Table
	 *
	 * @param	string	$table		Table name
	 * @param	bool	$if_not_exists	Whether to add IF NOT EXISTS condition
	 * @param	array	$attributes	Associative array of table attributes
	 * @return	bool
	 */
	public function create_table($table, $if_not_exists = FALSE, array $attributes = array())
	{
		if ($table === '')
		{
			show_error('A table name is required for that operation.');
		}
		else
		{
			$table = $this->qb->prefix().$table;
		}

		if (count($this->fields) === 0)
		{
			show_error('Field information is required.');
		}

		$sql = $this->_create_table($table, $if_not_exists, $attributes);

		if (is_bool($sql))
		{
			$this->_reset();
			if ($sql === FALSE)
			{
				return ($this->db->db_debug) ? $this->db->display_error('db_unsupported_feature') : FALSE;
			}
		}

		if (($result = $this->db->query($sql)) !== FALSE)
		{
			empty($this->db->data_cache['table_names']) OR $this->db->data_cache['table_names'][] = $table;

			// Most databases don't support creating indexes from within the CREATE TABLE statement
			if ( ! empty($this->keys))
			{
				for ($i = 0, $sqls = $this->_process_indexes($table), $c = count($sqls); $i < $c; $i++)
				{
					$this->db->query($sqls[$i]);
				}
			}
		}

		$this->_reset();
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Create Table
	 *
	 * @param	string	$table		Table name
	 * @param	bool	$if_not_exists	Whether to add 'IF NOT EXISTS' condition
	 * @param	array	$attributes	Associative array of table attributes
	 * @return	mixed
	 */
	protected function _create_table($table, $if_not_exists, $attributes)
	{
		if ($if_not_exists === TRUE && $this->_create_table_if === FALSE){
			if ($this->db->table_exists($table)){
				return TRUE;
			}
			else {
				$if_not_exists = FALSE;
			}
		}

		$sql = ($if_not_exists)
			? sprintf($this->_create_table_if, $this->qb->escape_identifiers($table))
			: 'CREATE TABLE';

		$columns = $this->_process_fields(TRUE);
		for ($i = 0, $c = count($columns); $i < $c; $i++)
		{
			$columns[$i] = ($columns[$i]['_literal'] !== FALSE)
					? "\n\t".$columns[$i]['_literal']
					: "\n\t".$this->_process_column($columns[$i]);
		}

		$columns = implode(',', $columns)
				.$this->_process_primary_keys($table);

		// Are indexes created from within the CREATE TABLE statement? (e.g. in MySQL)
		if ($this->_create_table_keys === TRUE)
		{
			$columns .= $this->_process_indexes($table);
		}

		// _create_table will usually have the following format: "%s %s (%s\n)"
		$sql = sprintf($this->_create_table.'%s',
			$sql,
			$this->qb->escape_identifiers($table),
			$columns,
			$this->_create_table_attr($attributes)
		);

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop Table
	 *
	 * @param	string	$table_name	Table name
	 * @param	bool	$if_exists	Whether to add an IF EXISTS condition
	 * @return	bool
	 */
	public function drop_table($table_name, $if_exists = FALSE)
	{
		if ($table_name === '')
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_table_name_required') : FALSE;
		}

		if (($query = $this->_drop_table($this->qb->prefix().$table_name, $if_exists)) === TRUE)
		{
			return TRUE;
		}

		$query = $this->db->query($query);

		// Update table list cache
		if ($query && ! empty($this->db->data_cache['table_names']))
		{
			$key = array_search(strtolower($this->qb->prefix().$table_name), array_map('strtolower', $this->db->data_cache['table_names']), TRUE);
			if ($key !== FALSE)
			{
				unset($this->db->data_cache['table_names'][$key]);
			}
		}

		return $query;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop Table
	 *
	 * Generates a platform-specific DROP TABLE string
	 *
	 * @param	string	$table		Table name
	 * @param	bool	$if_exists	Whether to add an IF EXISTS condition
	 * @return	string
	 */
	protected function _drop_table($table, $if_exists)
	{
		$sql = 'DROP TABLE';

		if ($if_exists)
		{
			if ($this->_drop_table_if === FALSE)
			{
				if ( ! $this->db->table_exists($table))
				{
					return TRUE;
				}
			}
			else
			{
				$sql = sprintf($this->_drop_table_if, $this->qb->escape_identifiers($table));
			}
		}

		return $sql.' '.$this->qb->escape_identifiers($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Rename Table
	 *
	 * @param	string	$table_name	Old table name
	 * @param	string	$new_table_name	New table name
	 * @return	bool
	 */
	public function rename_table($table_name, $new_table_name)
	{
		if ($table_name === '' OR $new_table_name === '')
		{
			show_error('A table name is required for that operation.');
			return FALSE;
		}
		elseif ($this->_rename_table === FALSE)
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unsupported_feature') : FALSE;
		}

		$result = $this->db->query(sprintf($this->_rename_table,
						$this->qb->escape_identifiers($this->qb->prefix().$table_name),
						$this->qb->escape_identifiers($this->qb->prefix().$new_table_name))
					);

		if ($result && ! empty($this->db->data_cache['table_names']))
		{
			$key = array_search(strtolower($this->qb->prefix().$table_name), array_map('strtolower', $this->db->data_cache['table_names']), TRUE);
			if ($key !== FALSE)
			{
				$this->db->data_cache['table_names'][$key] = $this->qb->prefix().$new_table_name;
			}
		}

		return $result;
	}

	// --------------------------------------------------------------------

	public function field_exists( $fieldName, $table )
	{
		$return = FALSE;
		$sql = 'SHOW COLUMNS FROM '. $this->qb->prefix().$table;

		$currentFields = array();
		$result = $this->db->query( $sql );

		if( ! $result ){
			return $return;
		}

		foreach( $result as $r ){
			if( array_key_exists('Field', $r) ){ // mysql
				$thisFieldName = $r['Field'];
			}
			else { // sqlite
				$thisFieldName = $r['name'];
			}

			$currentFields[ $thisFieldName ] = $r;
		}

		if( array_key_exists($fieldName, $currentFields) ){
			$return = TRUE;
		}

		return $return;
	}

	public function add_index( $table, $field )
	{
		$dbPrefix = $this->qb->prefix();
		$sql = "ALTER TABLE `{PRFX}$table` ADD INDEX (`$field`)";
		$sql = str_replace('{PRFX}', $dbPrefix, $sql );
		$this->db->query($sql);
	}

	/**
	 * Column Add
	 *
	 * @todo	Remove deprecated $_after option in 3.1+
	 * @param	string	$table	Table name
	 * @param	array	$field	Column definition
	 * @param	string	$_after	Column for AFTER clause (deprecated)
	 * @return	bool
	 */
	public function add_column( $table, $field, $_after = NULL )
	{
		// Work-around for literal column definitions
		is_array($field) OR $field = array($field);

		foreach( array_keys($field) as $k ){
			if( $this->field_exists($k, $table) ){
				continue;
			}

			// Backwards-compatibility work-around for MySQL/CUBRID AFTER clause (remove in 3.1+)
			if ($_after !== NULL && is_array($field[$k]) && ! isset($field[$k]['after'])){
				$field[$k]['after'] = $_after;
			}

			$this->add_field(array($k => $field[$k]));
		}

		$sqls = $this->_alter_table('ADD', $this->qb->prefix().$table, $this->_process_fields());
		$this->_reset();
		if ($sqls === FALSE){
			return ($this->db->db_debug) ? $this->db->display_error('db_unsupported_feature') : FALSE;
		}

		for ($i = 0, $c = count($sqls); $i < $c; $i++){
			if ($this->db->query($sqls[$i]) === FALSE){
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Column Drop
	 *
	 * @param	string	$table		Table name
	 * @param	string	$column_name	Column name
	 * @return	bool
	 */
	public function drop_column($table, $column_name)
	{
		$sql = $this->_alter_table('DROP', $this->qb->prefix().$table, $column_name);
		if ($sql === FALSE)
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unsupported_feature') : FALSE;
		}

		return $this->db->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Column Modify
	 *
	 * @param	string	$table	Table name
	 * @param	string	$field	Column definition
	 * @return	bool
	 */
	public function modify_column($table, $field)
	{
		// Work-around for literal column definitions
		is_array($field) OR $field = array($field);

		foreach (array_keys($field) as $k)
		{
			$this->add_field(array($k => $field[$k]));
		}

		if (count($this->fields) === 0)
		{
			show_error('Field information is required.');
		}

		$sqls = $this->_alter_table('CHANGE', $this->qb->prefix().$table, $this->_process_fields());
		$this->_reset();
		if ($sqls === FALSE)
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unsupported_feature') : FALSE;
		}

		for ($i = 0, $c = count($sqls); $i < $c; $i++)
		{
			if ($this->db->query($sqls[$i]) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}


	/**
	 * Process fields
	 *
	 * @param	bool	$create_table
	 * @return	array
	 */
	protected function _process_fields($create_table = FALSE)
	{
		$fields = array();

		foreach ($this->fields as $key => $attributes)
		{
			if (is_int($key) && ! is_array($attributes))
			{
				$fields[] = array('_literal' => $attributes);
				continue;
			}

			$attributes = array_change_key_case($attributes, CASE_UPPER);

			if ($create_table === TRUE && empty($attributes['TYPE']))
			{
				continue;
			}

			isset($attributes['TYPE']) && $this->_attr_type($attributes);

			$field = array(
				'name'			=> $key,
				'new_name'		=> isset($attributes['NAME']) ? $attributes['NAME'] : NULL,
				'type'			=> isset($attributes['TYPE']) ? $attributes['TYPE'] : NULL,
				'length'		=> '',
				'unsigned'		=> '',
				'null'			=> '',
				'unique'		=> '',
				'default'		=> '',
				'auto_increment'	=> '',
				'_literal'		=> FALSE
			);

			isset($attributes['TYPE']) && $this->_attr_unsigned($attributes, $field);

			if ($create_table === FALSE)
			{
				if (isset($attributes['AFTER']))
				{
					$field['after'] = $attributes['AFTER'];
				}
				elseif (isset($attributes['FIRST']))
				{
					$field['first'] = (bool) $attributes['FIRST'];
				}
			}

			$this->_attr_default($attributes, $field);

			if (isset($attributes['NULL']))
			{
				if ($attributes['NULL'] === TRUE)
				{
					$field['null'] = empty($this->_null) ? '' : ' '.$this->_null;
				}
				else
				{
					$field['null'] = ' NOT NULL';
				}
			}
			elseif ($create_table === TRUE)
			{
				$field['null'] = ' NOT NULL';
			}

			$this->_attr_auto_increment($attributes, $field);
			$this->_attr_unique($attributes, $field);

			if (isset($attributes['COMMENT']))
			{
				$field['comment'] = $this->db->escape($attributes['COMMENT']);
			}

			if (isset($attributes['TYPE']) && ! empty($attributes['CONSTRAINT']))
			{
				switch (strtoupper($attributes['TYPE']))
				{
					case 'ENUM':
					case 'SET':
						$attributes['CONSTRAINT'] = $this->db->escape($attributes['CONSTRAINT']);
					default:
						$field['length'] = is_array($attributes['CONSTRAINT'])
							? '('.implode(',', $attributes['CONSTRAINT']).')'
							: '('.$attributes['CONSTRAINT'].')';
						break;
				}
			}

			$fields[] = $field;
		}

		return $fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute TYPE
	 *
	 * Performs a data type mapping between different databases.
	 *
	 * @param	array	&$attributes
	 * @return	void
	 */
	protected function _attr_type(&$attributes)
	{
		// Usually overridden by drivers
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute UNSIGNED
	 *
	 * Depending on the _unsigned property value:
	 *
	 *	- TRUE will always set $field['unsigned'] to 'UNSIGNED'
	 *	- FALSE will always set $field['unsigned'] to ''
	 *	- array(TYPE) will set $field['unsigned'] to 'UNSIGNED',
	 *		if $attributes['TYPE'] is found in the array
	 *	- array(TYPE => UTYPE) will change $field['type'],
	 *		from TYPE to UTYPE in case of a match
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_unsigned(&$attributes, &$field)
	{
		if (empty($attributes['UNSIGNED']) OR $attributes['UNSIGNED'] !== TRUE)
		{
			return;
		}

		// Reset the attribute in order to avoid issues if we do type conversion
		$attributes['UNSIGNED'] = FALSE;

		if (is_array($this->_unsigned))
		{
			foreach (array_keys($this->_unsigned) as $key)
			{
				if (is_int($key) && strcasecmp($attributes['TYPE'], $this->_unsigned[$key]) === 0)
				{
					$field['unsigned'] = ' UNSIGNED';
					return;
				}
				elseif (is_string($key) && strcasecmp($attributes['TYPE'], $key) === 0)
				{
					$field['type'] = $key;
					return;
				}
			}

			return;
		}

		$field['unsigned'] = ($this->_unsigned === TRUE) ? ' UNSIGNED' : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute DEFAULT
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_default(&$attributes, &$field)
	{
		if ($this->_default === FALSE)
		{
			return;
		}

		if (array_key_exists('DEFAULT', $attributes))
		{
			if ($attributes['DEFAULT'] === NULL)
			{
				$field['default'] = empty($this->_null) ? '' : $this->_default.$this->_null;

				// Override the NULL attribute if that's our default
				$attributes['NULL'] = TRUE;
				$field['null'] = empty($this->_null) ? '' : ' '.$this->_null;
			}
			else
			{
				$field['default'] = $this->_default.$this->qb->escape($attributes['DEFAULT']);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute UNIQUE
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_unique(&$attributes, &$field)
	{
		if ( ! empty($attributes['UNIQUE']) && $attributes['UNIQUE'] === TRUE)
		{
			$field['unique'] = ' UNIQUE';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute AUTO_INCREMENT
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_auto_increment(&$attributes, &$field)
	{
		if ( ! empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE && stripos($field['type'], 'int') !== FALSE)
		{
			$field['auto_increment'] = ' AUTO_INCREMENT';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Process primary keys
	 *
	 * @param	string	$table	Table name
	 * @return	string
	 */
	protected function _process_primary_keys($table)
	{
		$sql = '';

		for ($i = 0, $c = count($this->primary_keys); $i < $c; $i++)
		{
			if ( ! isset($this->fields[$this->primary_keys[$i]]))
			{
				unset($this->primary_keys[$i]);
			}
		}

		if (count($this->primary_keys) > 0)
		{
			$sql .= ",\n\tCONSTRAINT ".$this->qb->escape_identifiers('pk_'.$table)
				.' PRIMARY KEY('.implode(', ', $this->qb->escape_identifiers($this->primary_keys)).')';
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Reset
	 *
	 * Resets table creation vars
	 *
	 * @return	void
	 */
	protected function _reset()
	{
		$this->fields = $this->keys = $this->primary_keys = array();
	}

	// --------------------------------------------------------------------

	/**
	 * CREATE TABLE attributes
	 *
	 * @param	array	$attributes	Associative array of table attributes
	 * @return	string
	 */
	protected function _create_table_attr($attributes)
	{
		$sql = '';

		foreach (array_keys($attributes) as $key)
		{
			if (is_string($key))
			{
				$sql .= ' '.strtoupper($key).' = '.$attributes[$key];
			}
		}

		if ( ! empty($this->db->char_set) && ! strpos($sql, 'CHARACTER SET') && ! strpos($sql, 'CHARSET'))
		{
			$sql .= ' DEFAULT CHARACTER SET = '.$this->db->char_set;
		}

		if ( ! empty($this->db->dbcollat) && ! strpos($sql, 'COLLATE'))
		{
			$sql .= ' COLLATE = '.$this->db->dbcollat;
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * ALTER TABLE
	 *
	 * @param	string	$alter_type	ALTER type
	 * @param	string	$table		Table name
	 * @param	mixed	$field		Column definition
	 * @return	string|string[]
	 */
	protected function _parent_alter_table($alter_type, $table, $field)
	{
		$sql = 'ALTER TABLE '.$this->qb->escape_identifiers($table).' ';

		// DROP has everything it needs now.
		if ($alter_type === 'DROP')
		{
			return $sql.'DROP COLUMN '.$this->qb->escape_identifiers($field);
		}

		$sql .= ($alter_type === 'ADD')
			? 'ADD '
			: $alter_type.' COLUMN ';

		$sqls = array();
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			$sqls[] = $sql
				.($field[$i]['_literal'] !== FALSE ? $field[$i]['_literal'] : $this->_process_column($field[$i]));
		}

		return $sqls;
	}

	// --------------------------------------------------------------------

	/**
	 * ALTER TABLE
	 *
	 * @param	string	$alter_type	ALTER type
	 * @param	string	$table		Table name
	 * @param	mixed	$field		Column definition
	 * @return	string|string[]
	 */
	protected function _alter_table($alter_type, $table, $field)
	{
		if ($alter_type === 'DROP')
		{
			return $this->_parent_alter_table($alter_type, $table, $field);
		}

		$sql = 'ALTER TABLE '.$this->qb->escape_identifiers($table);
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			if ($field[$i]['_literal'] !== FALSE)
			{
				$field[$i] = ($alter_type === 'ADD')
						? "\n\tADD ".$field[$i]['_literal']
						: "\n\tMODIFY ".$field[$i]['_literal'];
			}
			else
			{
				if ($alter_type === 'ADD')
				{
					$field[$i]['_literal'] = "\n\tADD ";
				}
				else
				{
					$field[$i]['_literal'] = empty($field[$i]['new_name']) ? "\n\tMODIFY " : "\n\tCHANGE ";
				}

				$field[$i] = $field[$i]['_literal'].$this->_process_column($field[$i]);
			}
		}

		return array($sql.implode(',', $field));
	}

	// --------------------------------------------------------------------

	/**
	 * Process column
	 *
	 * @param	array	$field
	 * @return	string
	 */
	protected function _process_column($field)
	{
		$extra_clause = isset($field['after'])
			? ' AFTER '.$this->qb->escape_identifiers($field['after']) : '';

		if (empty($extra_clause) && isset($field['first']) && $field['first'] === TRUE)
		{
			$extra_clause = ' FIRST';
		}

		return $this->qb->escape_identifiers($field['name'])
			.(empty($field['new_name']) ? '' : ' '.$this->qb->escape_identifiers($field['new_name']))
			.' '.$field['type'].$field['length']
			.$field['unsigned']
			.$field['null']
			.$field['default']
			.$field['auto_increment']
			.$field['unique']
			.(empty($field['comment']) ? '' : ' COMMENT '.$field['comment'])
			.$extra_clause;
	}

	// --------------------------------------------------------------------

	/**
	 * Process indexes
	 *
	 * @param	string	$table	(ignored)
	 * @return	string
	 */
	protected function _process_indexes($table)
	{
		$sql = '';

		for ($i = 0, $c = count($this->keys); $i < $c; $i++)
		{
			if (is_array($this->keys[$i]))
			{
				for ($i2 = 0, $c2 = count($this->keys[$i]); $i2 < $c2; $i2++)
				{
					if ( ! isset($this->fields[$this->keys[$i][$i2]]))
					{
						unset($this->keys[$i][$i2]);
						continue;
					}
				}
			}
			elseif ( ! isset($this->fields[$this->keys[$i]]))
			{
				unset($this->keys[$i]);
				continue;
			}

			is_array($this->keys[$i]) OR $this->keys[$i] = array($this->keys[$i]);

			$sql .= ",\n\tKEY ".$this->qb->escape_identifiers(implode('_', $this->keys[$i]))
				.' ('.implode(', ', $this->qb->escape_identifiers($this->keys[$i])).')';
		}

		$this->keys = array();

		return $sql;
	}
}
