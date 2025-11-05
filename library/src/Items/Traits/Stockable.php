<?php
	/*
	 * Copyright (c) 2025 FenclWebDesign.com
	 * This script may not be copied, reproduced or altered in whole or in part.
	 * We check the Internet regularly for illegal copies of our scripts.
	 * Do not edit or copy this script for someone else, because you will be held responsible as well.
	 * This copyright shall be enforced to the full extent permitted by law.
	 * Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	 * @Author: Deryk
	 */
	
	namespace Items\Traits;
	
	trait Stockable {
		protected int $stock_quantity;

		/**
		 * @return bool
		 */
		public function isOutOfStock(): bool {
			return $this->getAvailableQuantity() <= 0;
		}
		
		/**
		 * @return int
		 */
		public function getAvailableQuantity(): int {
			return $this->getStockQuantity() - $this->getSoldQuantity();
		}
		
		/**
		 * @return int
		 */
		public function getStockQuantity(): int {
			return $this->stock_quantity;
		}
		
		/**
		 * @return int
		 */
		public function getSoldQuantity(): int {
			// TODO: Calculate units sold
			return 0;
		}
		
		/**
		 * @param int $amount
		 *
		 * @return bool
		 */
		public function isLowStock(int $amount = 10): bool {
			return $this->getAvailableQuantity() > 0 && $this->getAvailableQuantity() <= $amount;
		}
		
		/**
		 * @return bool
		 */
		public function isInStock(): bool {
			return $this->getAvailableQuantity() > 0;
		}
	}
	