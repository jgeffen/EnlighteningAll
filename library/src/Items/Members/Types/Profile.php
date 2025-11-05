<?php
	/*
	Copyright (c) 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/
	
	/** @noinspection PhpUnused */
	
	namespace Items\Members\Types;
	
	use Database;
	use Exception;
	use Items\Abstracts;
	
	class Profile extends Abstracts\Member {
        public ?string $status = null;
        public ?string $created_at = null;
        public ?string $role = null;

        /**
		 * @param null|int $id
		 *
		 * @return $this
		 *
		 * @throws Exception
		 */
		public static function FromId(?int $id): self {
			$instance = Database::Action("SELECT * FROM `members` WHERE `username` = :username", array(
				'username' => $id
			))->fetchObject(self::class) ?: NULL;
			
			if(is_null($instance)) throw new Exception('Member not found.');
			
			return $instance;
		}
		
		/**
		 * @param null|string $username
		 *
		 * @return $this
		 *
		 * @throws Exception
		 */
		public static function FromUsername(?string $username): self {
			$instance = Database::Action("SELECT * FROM `members` WHERE `username` = :username", array(
				'username' => $username
			))->fetchObject(self::class) ?: NULL;
			
			if(is_null($instance)) throw new Exception('Member not found.');
			
			return $instance;
		}
	}