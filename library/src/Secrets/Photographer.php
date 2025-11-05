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
	
	class Photographer extends Abstracts\PageType {
		use Traits\Category, Traits\Gallery, Traits\Image, Traits\PDFs;
		
		protected Tables\Secrets $table = Tables\Secrets::PHOTOGRAPHERS;
		
		protected string  $name;
		protected ?string $email;
		protected ?string $phone;
		protected array   $packages;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `photographers` WHERE `id` = :id", array(
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
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}
		
		/**
		 * @return null|string
		 */
		public function getEmail(): ?string {
			return $this->email;
		}
		
		/**
		 * @return null|string
		 */
		public function getPhone(): ?string {
			return $this->phone;
		}
		
		/**
		 * @return Photographers\Package[]
		 */
		public function getPackages(): array {
			return Photographers\Package::FetchAll(Database::Action("SELECT * FROM `photographer_packages` WHERE `photographer_id` = :photographer_id ORDER BY `position`", array(
				'photographer_id' => $this->getId()
			)));
		}
	}