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

namespace Items\TravelAffiliateMembers;

use Database;
use Items;
use Items\Enums\Types;
use Items\Interfaces;
use Items\Traits;
use PDO;
use PDOStatement;

class Log implements Interfaces\Item {
	use Traits\Item;

	private string  $type;
	private int     $member_id;
	private ?string $table_name   = NULL;
	private ?int    $table_id     = NULL;
	private ?string $table_column = NULL;
	private ?string $notes    = NULL;

	/**
	 * @param null|int $id
	 *
	 * @return null|$this
	 */
	public static function Init(?int $id): ?self {
		return Database::Action("SELECT * FROM `travel_affiliate_member_logs` WHERE `id` = :id", array(
			'id' => $id
		))->fetchObject(self::class) ?: NULL;
	}

	/**
	 * @param PDOStatement $statement
	 *
	 * @return null|static
	 */
	public static function Fetch(PDOStatement $statement): ?self {
		return $statement->fetchObject(self::class) ?: NULL;
	}

	/**
	 * @param PDOStatement $statement
	 *
	 * @return self[]
	 */
	public static function FetchAll(PDOStatement $statement): array {
		return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
	}

	/**
	 * @return null|Types\Log
	 */
	public function getType(): ?Types\Log {
		return Types\Log::lookup($this->type);
	}

	/**
	 * @return int
	 */
	public function getMemberId(): int {
		return $this->member_id;
	}

	/**
	 * @return null|string
	 */
	public function getTableName(): ?string {
		return $this->table_name;
	}

	/**
	 * @return null|int
	 */
	public function getTableId(): ?int {
		return $this->table_id;
	}

	/**
	 * @return null|string
	 */
	public function getTableColumn(): ?string {
		return $this->table_column;
	}

	/**
	 * @return null|string
	 */
	public function getNotes(): ?string {
		return $this->notes;
	}
}
