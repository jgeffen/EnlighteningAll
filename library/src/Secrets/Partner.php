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
	
	namespace Secrets;
	
	use Database;
	use Items\Abstracts;
	use Items\Enums\Tables;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Partner extends Abstracts\Listed {
		use Traits\Image;
		
		protected Tables\Secrets $table = Tables\Secrets::PARTNERS;
		
		protected ?string $link;
		protected int     $position;
		protected bool    $analytics;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `partners` WHERE `id` = :id", array(
				'id' => $id
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
		 * @return null|string
		 */
		public function getAlt(): ?string {
			return !is_null($this->getHeading()) ? htmlspecialchars($this->getHeading(), ENT_COMPAT) : NULL;
		}
		
		/**
		 * @return null|string
		 */
		public function getLink(): ?string {
			return $this->link;
		}
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * @return bool
		 */
		public function hasAnalytics(): bool {
			return $this->analytics;
		}
	}