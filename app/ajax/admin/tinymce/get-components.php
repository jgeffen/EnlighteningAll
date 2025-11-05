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
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	try {
		// Variable Defaults
		$directories = sprintf("%s/html", dirname(__FILE__));
		
		// Iterate Over Directories
		foreach(new DirectoryIterator($directories) as $menuItem) {
			// Check for Dot
			if(!$menuItem->isDot()) {
				// Check for Directory
				if($menuItem->isDir()) {
					// Set Component
					$components[$menuItem->getFilename()] = array(
						'component' => Helpers::PrettyTitle($menuItem->getFilename()),
						'items'     => call_user_func(function(DirectoryIterator $subMenuItems): array {
							foreach($subMenuItems as $subMenuItem) {
								if($subMenuItem->isFile()) {
									$items[] = array(
										'type'  => 'menuitem',
										'text'  => Helpers::PrettyTitle($subMenuItem->getBasename('.html')),
										'value' => file_get_contents($subMenuItem->getPathname())
									);
								}
							}
							
							return $items ?? array();
						}, new DirectoryIterator($menuItem->getPathname())),
						'icon'      => match ($menuItem->getFilename()) {
							'buttons'       => 'template',
							'columns'       => 'table-caption',
							'layout'        => 'table-top-header',
							'page-elements' => 'table-merge-cells',
							default         => ''
						}
					);
				}
			}
		}
		
		// Set Response
		$json_response = array(
			'status'     => 'success',
			'components' => $components ?? array()
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);