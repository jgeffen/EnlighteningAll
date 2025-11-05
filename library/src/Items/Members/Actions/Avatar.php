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
	
	/** @noinspection PhpUnused */
	
	namespace Items\Members\Actions;
	
	use Database;
	use Error;
	use Helpers;
	use Items\Abstracts;
	use Items\Enums\Requests;
	use Items\Interfaces;
	use Items\Members;
	use JetBrains\PhpStorm\Pure;
	use PDOException;
	use UnhandledMatchError;
	
	class Avatar implements Interfaces\Action {
		private ?Members\Avatar  $avatar;
		private Abstracts\Member $member;
		private Requests\Avatar  $request;
		
		private null|string $filename;
		
		/**
		 * @param null|Members\Avatar $avatar
		 * @param Abstracts\Member    $member
		 */
		public function __construct(?Members\Avatar $avatar, Abstracts\Member $member) {
			$this->avatar = $avatar;
			$this->member = $member;
		}
		
		/**
		 * @param Abstracts\Member    $member
		 * @param null|Members\Avatar $avatar
		 *
		 * @return $this
		 */
		#[Pure] public static function Init(Abstracts\Member $member, ?Members\Avatar $avatar = NULL): self {
			return new self($avatar, $member);
		}
		
		/**
		 * @param Requests\Avatar $request
		 *
		 * @return $this
		 */
		public function setRequest(Requests\Avatar $request): self {
			$this->request = $request;
			return $this;
		}
		
		/**
		 * @param null|int|string $filename
		 *
		 * @return Avatar
		 */
		public function setFilename(int|string|null $filename): Avatar {
			$this->filename = $filename;
			return $this;
		}
		
		/**
		 * @return bool
		 *
		 * @throws Error|PDOException|UnhandledMatchError
		 */
		public function execute(): bool {
			return match ($this->getAction()) {
				Requests\Avatar::CREATE => $this->create(),
				Requests\Avatar::DELETE => $this->delete(),
				Requests\Avatar::UPDATE => $this->update()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function create(): bool {
			return Database::Action("INSERT INTO `member_avatars` SET `member_id` = :member_id, `filename` = :filename, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_id'  => $this->getMember()->getId(),
				'filename'   => $this->getFilename(),
				'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function delete(): bool {
			$directory = sprintf("%s/files/members/%d/avatar", dirname(__DIR__, 5), $this->getMember()->getId());
			$response  = Database::Action("DELETE FROM `member_avatars` WHERE `id` = :id AND `member_id` = :member_id", array(
				'id'        => $this->getAvatar()->getId(),
				'member_id' => $this->getMember()->getId()
			))->rowCount();
			
			Helpers::RemoveFile($directory, $this->getAvatar()->getFilename());
			
			return $response;
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function update(): bool {
			return Database::Action("UPDATE `member_avatars` SET `filename` = :filename, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `id` = :id AND `member_id` = :member_id", array(
				'filename'   => $this->getFilename(),
				'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
				'id'         => $this->getAvatar()?->getId(),
				'member_id'  => $this->getMember()->getId()
			))->rowCount();
		}
		
		/**
		 * @return null|Members\Avatar
		 */
		private function getAvatar(): ?Members\Avatar {
			return $this->avatar;
		}
		
		/**
		 * @return Abstracts\Member
		 */
		private function getMember(): Abstracts\Member {
			return $this->member;
		}
		
		/**
		 * @return Requests\Avatar
		 */
		private function getAction(): Requests\Avatar {
			return $this->request;
		}
		
		/**
		 * @return null|string
		 */
		private function getFilename(): ?string {
			return $this->filename;
		}
	}