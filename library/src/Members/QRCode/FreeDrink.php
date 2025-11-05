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
	
	namespace Members\QRCode;
	
	use Database;
	use Debug;
	use Endroid\QrCode\Builder\Builder;
	use Endroid\QrCode\Encoding\Encoding;
	use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
	use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
	use Endroid\QrCode\Writer\PngWriter;
	use Endroid\QrCode\Writer\Result;
	use Exception;
	use Items\Members;
	use Membership;
	
	class FreeDrink {
		public const TYPE   = self::class;
		public const EXPIRY = 60;
		
		private Membership      $member;
		private ?Members\QRCode $qr_code = NULL;
		
		/**
		 * @param Membership $member
		 */
		public function __construct(Membership $member) { $this->member = $member; }
		
		/**
		 * @param bool $exit
		 *
		 * @return null|Result\ResultInterface
		 */
		public function generate(bool $exit = FALSE): null|Result\ResultInterface {
			try {
				$this->qr_code = Members\QRCode::Fetch(Database::Action("SELECT * FROM `member_qr_codes` WHERE `type` = :type AND `member_id` = :member_id AND TIMESTAMPDIFF(MINUTE, GREATEST(`timestamp`, `last_timestamp`), NOW()) <= :minutes", array(
					'type'      => static::TYPE,
					'member_id' => $this->getMember()->getId(),
					'minutes'   => static::EXPIRY
				)));
				
				if(is_null($this->getQrCode())) {
					if($exit) throw new Exception(sprintf("Unable to Generate QR Code for Member ID %d", $this->getMember()->getId()));
					
					Database::Action("INSERT INTO `member_qr_codes` SET `type` = :type, `member_id` = :member_id, `hash` = :hash, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address ON DUPLICATE KEY UPDATE `hash` = :hash, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
						'type'       => static::TYPE,
						'member_id'  => $this->getMember()->getId(),
						'hash'       => strtoupper(md5(uniqid($this->getMember()->getEmail(), TRUE))),
						'author'     => NULL,
						'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
						'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
					));
					
					return $this->generate(TRUE);
				}
				
				return Builder::create()
				              ->writer(new PngWriter())
				              ->writerOptions(array())
				              ->data($this->getQrCode()->getHash())
				              ->encoding(new Encoding('UTF-8'))
				              ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
				              ->size(600)
				              ->margin(0)
				              ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
				              ->build();
			} catch(Exception $exception) {
				Debug::Exception($exception);
				return NULL;
			}
		}
		
		/**
		 * @return Membership
		 */
		public function getMember(): Membership {
			return $this->member;
		}
		
		/**
		 * @return null|Members\QRCode
		 */
		public function getQrCode(): ?Members\QRCode {
			return $this->qr_code;
		}
	}