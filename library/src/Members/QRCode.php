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
	
	namespace Members;
	
	use Membership;
	
	class QRCode {
		private Membership             $member;
		private QRCode\AccountApproval $account_approval;
		private QRCode\CheckIn         $check_in;
		private QRCode\FreeDrink       $free_drink;
		private QRCode\ProfileLink     $profile_link;
		
		/**
		 * @param Membership $member
		 */
		public function __construct(Membership $member) { $this->member = $member; }
		
		/**
		 * @return Membership
		 */
		public function getMember(): Membership {
			return $this->member;
		}
		
		/**
		 * @return QRCode\AccountApproval
		 */
		public function getAccountApproval(): QRCode\AccountApproval {
			return $this->account_approval ??= new QRCode\AccountApproval($this->getMember());
		}
		
		/**
		 * @return QRCode\CheckIn
		 */
		public function getCheckIn(): QRCode\CheckIn {
			return $this->check_in ??= new QRCode\CheckIn($this->getMember());
		}
		
		/**
		 * @return QRCode\FreeDrink
		 */
		public function getFreeDrink(): QRCode\FreeDrink {
			return $this->free_drink ??= new QRCode\FreeDrink($this->getMember());
		}
		
		/**
		 * @return QRCode\ProfileLink
		 */
		public function getProfileLink(): QRCode\ProfileLink {
			return $this->profile_link ??= new QRCode\ProfileLink($this->getMember());
		}
	}