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
	
	namespace Items;
	
	use Database;
	use Helpers;
	use Items;
	use Items\Abstracts;
	use Items\Enums\Statuses;
	use Items\Enums\Tables;
	use Items\Traits;
	use JetBrains\PhpStorm\Pure;
	use PDO;
	use PDOStatement;
	use Render;
	
	class Condo extends Abstracts\Listed {
		use Traits\Gallery, Traits\Image;
		
		protected Tables\Secrets $table = Tables\Secrets::CONDOS;
		
		protected string $status;
		protected float  $price;
		protected int    $position;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `condos` WHERE `id` = :id", array(
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
		 * @return null|Statuses\Condo
		 */
		public function getStatus(): ?Statuses\Condo {
			return Statuses\Condo::lookup($this->status);
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		#[Pure] public function getPrice(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			return !$format ? $this->price : Helpers::FormatCurrency($this->price, $currency, $locale);
		}
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * @return void
		 */
		public function setImages(): void {
			$this->images = Render::Images(array(
				'source'          => sprintf("/files/%s/%s", $this->table->getValue(), $this->getFilename()),
				'slide'           => sprintf("/files/%s/slide/%s", $this->table->getValue(), $this->getFilename()),
				'square'          => sprintf("/files/%s/square/%s", $this->table->getValue(), $this->getFilename()),
				'square_thumb'    => sprintf("/files/%s/square/thumbs/%s", $this->table->getValue(), $this->getFilename()),
				'landscape'       => sprintf("/files/%s/landscape/%s", $this->table->getValue(), $this->getFilename()),
				'landscape_thumb' => sprintf("/files/%s/landscape/thumbs/%s", $this->table->getValue(), $this->getFilename()),
				'portrait'        => sprintf("/files/%s/poster/%s", $this->table->getValue(), $this->getFilename()),
				'portrait_thumb'  => sprintf("/files/%s/poster/thumbs/%s", $this->table->getValue(), $this->getFilename()),
			));
		}
		
		/**
		 * @param string $index
		 *
		 * @return null|string
		 */
		public function getDefaultImage(string $index): ?string {
			return match ($index) {
				'slide'           => Items\Defaults::SLIDE,
				'square'          => Items\Defaults::SQUARE,
				'square_thumb'    => Items\Defaults::SQUARE_THUMB,
				'landscape'       => Items\Defaults::LANDSCAPE,
				'landscape_thumb' => Items\Defaults::LANDSCAPE_THUMB,
				'portrait'        => Items\Defaults::PORTRAIT,
				'portrait_thumb'  => Items\Defaults::PORTRAIT_THUMB,
				default           => NULL
			};
		}
		
		/**
		 * @return null|string
		 */
		public function getSlideImage(): ?string {
			return $this->getImage('slide');
		}
	}