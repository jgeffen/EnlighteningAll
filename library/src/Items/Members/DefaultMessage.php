<?php
/*
	Copyright (c) 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/

/** @noinspection PhpUnused */

namespace Items\Members;

use Database;
use Debug;
use Items\Enums\Options;
use Items\Enums\Types;
use Items\Interfaces;
use Items\Traits;
use PDO;
use PDOStatement;
use UnhandledMatchError;

class DefaultMessage implements Interfaces\Item {
	use Traits\Item;

	protected string $message;
	protected string $type;

	/**
	 * @param null|int $id
	 *
	 * @return null|static
	 */
	public static function Init(?int $id): ?static {
		return Database::Action("SELECT * FROM `member_default_message` WHERE `id` = :id", array(
			'id' => $id
		))->fetchObject(static::class) ?: NULL;
	}

	/**
	 * @param PDOStatement $statement
	 *
	 * @return null|static
	 */
	public static function Fetch(PDOStatement $statement): ?static {
		return $statement->fetchObject(static::class) ?: NULL;
	}

	/**
	 * @param PDOStatement $statement
	 *
	 * @return static[]
	 */
	public static function FetchAll(PDOStatement $statement): array {
		return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
	}

	/**
	 * @return string
	 */
	public function getMessage(): string {
		return $this->message;
	}


	/**
	 * @return null
	 */
	public function getType(): string{
		return $this->type;
	}
}
