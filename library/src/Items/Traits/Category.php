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
	
	trait Category {
		protected ?Items\Category $category;
		protected ?int            $category_id;
		
		/**
		 * @return null|Items\Category
		 */
		public function getCategory(): ?Items\Category {
			return $this->category ??= Items\Category::Init($this->getCategoryId());
		}
		
		/**
		 * @return null|int
		 */
		public function getCategoryId(): ?int {
			return $this->category_id;
		}
	}