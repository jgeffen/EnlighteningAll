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
	
	use Render;
	
	trait Gallery {
		use Image;
		
		/**
		 * @return array
		 */
		public function getGallery(): array {
			return Render::Gallery($this->table->getValue(), $this->getId(), $this->getFilename());
		}
		
		/**
		 * @return array
		 */
		public function getStaffGallery(): array {
			return Render::StaffGallery($this->table->getValue(), $this->getId(), $this->getFilename());
		}
		
		/**
		 * @param string $component
		 * @param array  $options
		 *
		 * @return void
		 */
		public function renderGallery(string $component = 'sliders/gallery-carousel/gallery-carousel', array $options = array()): void {
			Render::Component($component, array_merge(array(
				'item'               => $this,
				'inset'              => TRUE,
				'inset_position'     => 'right',
				'margin'             => '',
				'single_img_classes' => 'right inset border mt-0 mt-sm-0 mt-md-1'
			), $options));
		}
	}