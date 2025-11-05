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
	
	namespace Secrets\Clubs;
	
	use Database;
	use Items\Abstracts;
	use Items\Traits;
	use Items\Enums\Tables;
	use PDO;
	use PDOStatement;
	
	class State extends Abstracts\PageType {
		use Traits\Gallery, Traits\Image, Traits\PDFs;
		
		protected Tables\Secrets\Clubs $table = Tables\Secrets\Clubs::STATES;
		
		protected string $name;
		protected string $short_name;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `club_states` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param null|string $state
		 *
		 * @return null|$this
		 */
		public static function InitFromState(?string $state): ?self {
			return Database::Action("SELECT * FROM `club_states` WHERE `short_name` = :short_name", array(
				'short_name' => $state
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|self
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
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}
		
		/**
		 * @return string
		 */
		public function getShortName(): string {
			return $this->short_name;
		}
	}