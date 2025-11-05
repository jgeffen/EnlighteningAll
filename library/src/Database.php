<?php
/*
	Copyright (c) 2020, 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/

// TODO: Mask config from exposing database credentials.

use JetBrains\PhpStorm\Pure;

/**
 * Database
 */
class Database extends PDO {
	/**
	 * Database configuration loaded.
	 *
	 * @var object { "driver", "host", port, "dbname", "user", "pass" }
	 */
	private object $config;

	/**
	 * @param string|null $config_path
	 * @param array       $options
	 *
	 * @throws Exception
	 */
	public function __construct(?string $config_path = NULL, array $options = array()) {
		$config_path ??= sprintf("%s/settings/database.json", dirname(__DIR__));

		if (!$this->config = (object)json_decode(file_get_contents($config_path), TRUE)) throw new Exception(sprintf("Unable to open %s.", basename($config_path)));

		parent::__construct(
			sprintf("%s:host=%s;dbname=%s;charset=utf8mb4", $this->config->driver, $this->config->host, $this->config->dbname),
			$this->config->user,
			$this->config->pass,
			$options ?: array(
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT         => TRUE
			)
		);
	}

	/**
	 * Creates a new DataTables class
	 *
	 * @param string                    $table_name
	 * @param array                     $payload ['column' => 'value', ...]
	 * @param DataTables\Editor\Field[] $fields
	 * @param array                     $wheres
	 * @param string|null               $config_path
	 *
	 * @return array
	 *
	 * @throws Exception
	 * @link https://editor.datatables.net/manual/php/
	 */
	public static function DataTables(string $table_name, array $payload, array $fields = array(), array $wheres = array(), ?string $config_path = NULL): array {
		list(, $host, $port, $dbname, $user, $pass) = array_values((array)self::Config($config_path));

		$dtDatabase = new DataTables\Database(array(
			'user' => $user,
			'pass' => $pass,
			'host' => $host,
			'port' => $port,
			'db'   => $dbname,
			'type' => 'Mysql'
		));

		$dtEditor = new DataTables\Editor($dtDatabase, $table_name);

		array_map(fn ($where) => $dtEditor->where(...$where), $wheres);

		$columns = array_map(
			fn ($column) => new DataTables\Editor\Field($column),
			Database::Action("SELECT `column_name` FROM `information_schema`.`columns` WHERE `table_schema` = :table_schema AND `table_name` = :table_name", array(
				'table_schema' => Database::Action("SELECT IFNULL(DATABASE(), '_') AS `table_schema`")->fetch(PDO::FETCH_COLUMN),
				'table_name'   => $table_name
			))->fetchAll(PDO::FETCH_COLUMN)
		);

		return $dtEditor->fields(...array_unique(array_merge($columns, $fields), SORT_REGULAR))->process($payload)->debug()->data();
	}

	/**
	 * @param string|null $config_path
	 *
	 * @return object { "driver", "host", port, "dbname", "user", "pass" }
	 *
	 * @throws Exception
	 */
	public static function Config(?string $config_path = NULL): object {
		$config_path ??= sprintf("%s/settings/database.json", dirname(__DIR__));

		if (!$config = (object)json_decode(file_get_contents($config_path), TRUE)) throw new Exception(sprintf("Unable to open %s.", basename($config_path)));

		return $config;
	}

	/**
	 * Performs query and returns PDOStatement object or false on no result.
	 *
	 * @param string $query
	 * @param array  $params
	 * @param bool   $return_id
	 *
	 * @return bool|string|PDOStatement
	 */
	public static function Action(string $query, array $params = array(), bool $return_id = FALSE): bool|string|PDOStatement {
		$database  = new Database();
		$statement = $database->performQuery($query, $params);

		return !$return_id ? $statement : $database->lastInsertId();
	}

	/**
	 * Performs query and returns PDOStatement object or false on no result.
	 *
	 * @param string $query
	 * @param array  $params
	 *
	 * @return bool|PDOStatement
	 */
	public function performQuery(string $query, array $params = array()): bool|PDOStatement {
		if (str_starts_with($query, 'SELECT')) {
			foreach ($params as $param => $value) {
				if (is_null($value)) {
					$query = preg_replace("/!= :$param/", sprintf("IS NOT :%s", $param), $query);
					$query = preg_replace("/= :$param/", sprintf("IS :%s", $param), $query);
				}
			}
		}

		$statement = $this->prepare($query);

		foreach ($params as $param => $value) {
			$statement->bindValue(sprintf(":%s", $param), $value, match (gettype($value)) {
				'boolean' => PDO::PARAM_BOOL,
				'integer' => PDO::PARAM_INT,
				'NULL'    => PDO::PARAM_NULL,
				default   => PDO::PARAM_STR
			});
		}

		$statement->execute();

		return $statement;
	}

	/**
	 * SearchPanes is a powerful tool which is used to search a DataTable through a series of panes which are populated with options from the table.
	 * As of SearchPanes 1.1 server-side processing is supported, making SearchPanes useful for those with large data sets.
	 *
	 * @return DataTables\Editor\SearchPaneOptions
	 *
	 * @link https://editor.datatables.net/manual/php/searchpanes
	 */
	#[Pure] public static function SearchPane(): DataTables\Editor\SearchPaneOptions {
		return new DataTables\Editor\SearchPaneOptions;
	}

	/**
	 * Use the NVP to insert values into the database.
	 *
	 * @param string $table_name     Database table name.
	 * @param array  $payload        ['column' => 'value', ...]
	 * @param bool   $create_missing Whether the system creates the missing fields.
	 *
	 * @return string|false Last Insert ID
	 *
	 * @noinspection SqlResolve
	 */
	public static function ArrayInsert(string $table_name, array $payload, bool $create_missing = FALSE): string|bool {
		$database = new Database();

		if ($create_missing) {
			// Sort Data
			uksort($payload, 'strcasecmp');

			// Add Missing Columns
			array_map(function ($column) use ($table_name) {
				Database::Action(sprintf("ALTER TABLE `%s` ADD `%s` TEXT", $table_name, filter_var($column, FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
			}, array_keys(array_diff_key(array_reverse($payload, TRUE), Database::Action("SELECT `column_name` FROM `information_schema`.`columns` WHERE `table_schema` = :table_schema AND `table_name` = :table_name", array(
				'table_schema' => Database::Action("SELECT IFNULL(DATABASE(), '_') AS `table_schema`")->fetchColumn(),
				'table_name'   => $table_name
			))->fetchAll(PDO::FETCH_UNIQUE))));
		}

		// Variable Defaults
		$statement = join(', ', array_map(function ($column) {
			return sprintf("`%1\$s` = :%1\$s", filter_var($column, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
		}, array_keys($payload)));

		// Update Database
		$database->performQuery("INSERT INTO `$table_name` SET $statement", array_map(function ($value) {
			return is_array($value) || is_object($value) ? json_encode($value) : $value;
		}, $payload));

		return $database->lastInsertId();
	}

	/**
	 * Use the NVP to update values into the database.
	 *
	 * @param string $table_name     Database table name.
	 * @param array  $payload        ['column' => 'value', ...]
	 * @param array  $identifier     ['column' => 'value']
	 * @param bool   $create_missing Whether the system creates the missing fields.
	 *
	 * @return int Affected Rows
	 *
	 * @noinspection SqlResolve
	 * @noinspection PhpFormatFunctionParametersMismatchInspection
	 */
	public static function ArrayUpdate(string $table_name, array $payload, array $identifier, bool $create_missing = FALSE): int {
		if ($create_missing) {
			// Sort Data
			uksort($payload, 'strcasecmp');

			// Add Missing Columns
			array_map(function ($column) use ($table_name) {
				Database::Action(sprintf("ALTER TABLE `%s` ADD `%s` TEXT", $table_name, filter_var($column, FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
			}, array_keys(array_diff_key(array_reverse($payload, TRUE), Database::Action("SELECT `column_name` FROM `information_schema`.`columns` WHERE `table_schema` = :table_schema AND `table_name` = :table_name", array(
				'table_schema' => Database::Action("SELECT IFNULL(DATABASE(), '_') AS `table_schema`")->fetchColumn(),
				'table_name'   => $table_name
			))->fetchAll(PDO::FETCH_UNIQUE))));
		}

		// Variable Defaults
		$statement = join(', ', array_map(function ($column) {
			return sprintf("`%1\$s` = :%1\$s", filter_var($column, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
		}, array_keys($payload)));

		// Update Database
		return Database::Action(sprintf("UPDATE `$table_name` SET $statement WHERE `%1\$s` = :%1\$s", filter_var(array_keys($identifier)[0], FILTER_SANITIZE_FULL_SPECIAL_CHARS)), array_map(function ($value) {
			return is_array($value) || is_object($value) ? json_encode($value) : $value;
		}, $payload + $identifier))->rowCount();
	}

	/**
	 * Outputs query as a string for debugging.
	 *
	 * @param string $query
	 * @param array  $params
	 *
	 * @return string
	 */
	public static function Debug(string $query, array $params = array()): string {
		return (new Database())->performDebug($query, $params);
	}

	/**
	 * Outputs query as a string for debugging.
	 *
	 * @param string $query
	 * @param array  $params
	 *
	 * @return string
	 */
	public function performDebug(string $query, array $params = array()): string {
		foreach ($params as $param => $value) {
			$query = preg_replace("/:$param/", match (gettype($value)) {
				'boolean' => $value ? 'TRUE' : 'FALSE',
				'integer' => $value,
				'NULL'    => 'NULL',
				default   => $this->quote($value)
			}, $query);
		}

		return str_replace('= NULL', 'IS NULL', str_replace('!= NULL', 'IS NOT NULL', $query));
	}
}
