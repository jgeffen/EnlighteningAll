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
	
	use Items\Product;
	
	// Input
	$upc = filter_input(INPUT_POST, 'upc');
	
	// Look up product_id by UPC
	$product_id = Database::Action(
		"SELECT product_id FROM product_upcs WHERE upc_code = :upc LIMIT 1",
		array('upc' => $upc)
	)->fetchColumn();
	
	if (!$product_id) {
		echo json_encode(array(
			'status'  => 'error',
			'message' => 'Product not found'
		));
		exit;
	}
	
	$product = Product::Init($product_id);
	
	echo json_encode(array(
		'status'    => 'success',
		'id'        => $product->getId(),
		'label'     => $product->getLabel(),
		'price'     => $product->getPrice(),
		'is_taxable'=> $product->isTaxable(),
		'tax_rate'  => $product->getSalesTax()
	));


