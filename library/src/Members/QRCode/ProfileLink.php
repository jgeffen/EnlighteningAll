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
	
	use Endroid\QrCode\Builder\Builder;
	use Endroid\QrCode\Encoding\Encoding;
	use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
	use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
	use Endroid\QrCode\Writer\PngWriter;
	use Endroid\QrCode\Writer\Result;
	use Membership;
	
	class ProfileLink {
		private Membership $member;
		
		/**
		 * @param Membership $member
		 */
		public function __construct(Membership $member) { $this->member = $member; }
		
		/**
		 * @return Result\ResultInterface
		 */
		public function generate(): Result\ResultInterface {
			return Builder::create()
			              ->writer(new PngWriter())
			              ->writerOptions(array())
			              ->data($this->member->getLink(TRUE))
			              ->encoding(new Encoding('UTF-8'))
			              ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
			              ->size(600)
			              ->margin(10)
			              ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
			              ->build();
		}
	}