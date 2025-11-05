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
	
	namespace Items\Traits;
	
	use Items;
	use Render;
	
	trait Image {
		protected ?string $filename;
		protected ?string $filename_alt;
		protected array   $images;
		
		/**
		 * @return null|string
		 */
		public function getFilename(): ?string {
			return $this->filename;
		}
		
		/**
		 * @return void
		 */
		public function setImages(): void {
			$this->images = Render::Images(array(
				'source'          => sprintf("/files/%s/%s", $this->table->getValue(), $this->getFilename()),
				'square'          => sprintf("/files/%s/square/%s", $this->table->getValue(), $this->getFilename()),
				'square_thumb'    => sprintf("/files/%s/square/thumbs/%s", $this->table->getValue(), $this->getFilename()),
				'landscape'       => sprintf("/files/%s/landscape/%s", $this->table->getValue(), $this->getFilename()),
				'landscape_thumb' => sprintf("/files/%s/landscape/thumbs/%s", $this->table->getValue(), $this->getFilename()),
				'portrait'        => sprintf("/files/%s/poster/%s", $this->table->getValue(), $this->getFilename()),
				'portrait_thumb'  => sprintf("/files/%s/poster/thumbs/%s", $this->table->getValue(), $this->getFilename()),
			));
		}
		
		/**
		 * @return array
		 */
		public function getImages(): array {
			!isset($this->images) && $this->setImages();
			return $this->images;
		}
		
		/**
		 * @param string $index
		 *
		 * @return null|string
		 */
		public function getImage(string $index = 'source'): ?string {
			return $this->getImages()[$index] ?? $this->getDefaultImage($index);
		}
		
		/**
		 * @return bool
		 */
		public function hasImage(): bool {
			return !empty($this->getImage());
		}
		
		/**
		 * @param string $index
		 *
		 * @return null|string
		 */
		public function getDefaultImage(string $index): ?string {
			return match ($index) {
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
		public function getSquareImage(): ?string {
			return $this->getImage('square');
		}
		
		/**
		 * @return null|string
		 */
		public function getSquareThumb(): ?string {
			return $this->getImage('square_thumb');
		}
		
		/**
		 * @return null|string
		 */
		public function getLandscapeImage(): ?string {
			return $this->getImage('landscape');
		}
		
		/**
		 * @return null|string
		 */
		public function getLandscapeThumb(): ?string {
			return $this->getImage('landscape_thumb');
		}
		
		/**
		 * @return null|string
		 */
		public function getPortraitImage(): ?string {
			return $this->getImage('portrait');
		}
		
		/**
		 * @return null|string
		 */
		public function getPortraitThumb(): ?string {
			return $this->getImage('portrait_thumb');
		}
		
		/**
		 * @return null|string
		 */
		public function getFilenameAlt(): ?string {
			return !is_null($this->filename_alt) ? htmlspecialchars($this->filename_alt, ENT_COMPAT) : NULL;
		}
		
		/**
		 * Alias for method getFileNameAlt();
		 *
		 * @return null|string
		 */
		public function getImageAlt(): ?string {
			return $this->getFilenameAlt();
		}
	}