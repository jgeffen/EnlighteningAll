<?php
	/*
	Copyright (c) 2020, 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/
	
	class Form {
		private const DEFAULTS = array(
			'id'        => '',
			'class'     => 'form-control',
			'label'     => '',
			'maxlength' => 255,
			'name'      => '',
			'tooltip'   => '',
			'type'      => 'text',
			'value'     => ''
		);
		
		private const DEFAULTS_INPUT = array(
			'id'        => '',
			'class'     => 'form-control',
			'label'     => '',
			'maxlength' => 255,
			'name'      => '',
			'tooltip'   => '',
			'type'      => 'text',
			'value'     => ''
		);
		
		/**
		 * Renders Bootstrap 4 field.
		 *
		 * @param string $field
		 * @param array  $options
		 */
		public static function Field(string $field, array $options = array()): void {
			$path    = sprintf("%s/components/forms/fields", dirname(__DIR__));
			$options = array_merge(self::DEFAULTS, $options);
			
			if((include(sprintf("%s/%s.php", $path, $field))) === FALSE) {
				echo '[ NOT FOUND ]';
			}
		}
		
		/**
		 * Renders Bootstrap 4 input field.
		 *
		 *
		 * @param array $options
		 *
		 * @link         https://getbootstrap.com/docs/4.6/components/forms/
		 */
		public static function Input(array $options = array()): void {
			$options = array_merge(self::DEFAULTS_INPUT, $options);
			
			if((include(sprintf("%s/components/forms/fields/input.php", dirname(__DIR__)))) === FALSE) {
				echo '[ NOT FOUND ]';
			}
		}
		
		/**
		 * Renders Bootstrap 4 fields grouped in a section.
		 *
		 * @param string $section
		 * @param array  $item
		 *
		 * @noinspection PhpUnusedParameterInspection
		 */
		public static function Section(string $section, array $item = array()): void {
			$path = sprintf("%s/components/forms/sections", dirname(__DIR__));
			
			include(sprintf("%s/%s.php", $path, $section));
		}
		
		/**
		 * @template TException
		 *
		 * @param bool                     $check
		 * @param string                   $message
		 * @param class-string<TException> $class
		 *
		 * @return void
		 *
		 * @throws TException
		 */
		public static function Condition(bool $check, string $message, string $class = Exception::class): void {
			if($check) throw new $class($message);
		}
	}