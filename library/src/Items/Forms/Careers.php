<?php
	/*
	Copyright (c) 2023 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	namespace Items\Forms;
	
	use Database;
	use Helpers;
	use Items\Form;
	
	class Careers extends Form {
		private ?string $resume;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `forms` WHERE `type` = 'careers' AND `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @return null|string
		 */
		public function getCaptcha(): ?string {
			return $this->captcha;
		}
		
		/**
		 * @return null|string
		 */
		public function getCareer(): ?string {
			return $this->career;
		}
		
		/**
		 * @return null|string
		 */
		public function getComments(): ?string {
			return $this->comments;
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
		public function getFilename(): ?string {
			return $this->filename;
		}
		
		/**
		 * @return null|string
		 */
		public function getName(): ?string {
			return $this->name;
		}
		
		/**
		 * @return null|string
		 */
		public function getPhone(): ?string {
			return $this->phone;
		}
		
		/**
		 * @return null|string
		 */
		public function getResume(): ?string {
			$file = sprintf("/files/resumes/%s", $this->filename);
			
			return $this->resume ??= is_file(Helpers::PathAbsolute($file)) ? $file : NULL;
		}
	}