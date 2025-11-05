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

class Setting implements Interfaces\Item {
	use Traits\Item;

	protected string $name;
	protected string $value;
	protected string $label;
	protected string $label_text;
	protected string $type;

	/**
	 * @param null|int $id
	 *
	 * @return null|static
	 */
	public static function Init(?int $id): ?static {
		return Database::Action("SELECT * FROM `member_settings` WHERE `id` = :id", array(
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
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return null|bool|array|string
	 */
	public function getValue(): null|bool|array|string {
		try {
			return match ($this->getType()) {
				Types\Setting::BOOLEAN => boolval(Options\OnOff::lookup($this->value)?->getValue()),
				Types\Setting::INTEGER => intval($this->value),
				Types\Setting::JSON    => json_decode($this->value, TRUE),
				Types\Setting::STRING  => $this->value
			};
		} catch (UnhandledMatchError $exception) {
			Debug::Exception($exception);
		}

		return NULL;
	}

	/**
	 * @return string
	 */
	public function getLabel(): string {
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function getLabelText(): string {
		return $this->label_text;
	}

	/**
	 * @return null|Types\Setting
	 */
	public function getType(): ?Types\Setting {
		return Types\Setting::lookup($this->type);
	}
}
