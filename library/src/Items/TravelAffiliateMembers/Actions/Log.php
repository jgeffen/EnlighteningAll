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

namespace Items\TravelAffiliateMembers\Actions;

use Database;
use Error;
use Items\Enums\Types;
use Items\Enums\Requests;
use Items\Interfaces;
use Items\TravelAffiliateMembers;
use JetBrains\PhpStorm\Pure;
use TravelAffiliateMembership;
use PDOException;
use UnhandledMatchError;

class Log implements Interfaces\Action {
	private ?TravelAffiliateMembers\Log $log;
	private TravelAffiliateMembership $member;
	private Requests\Log $action;

	private ?Interfaces\TableEnum $table_name   = NULL;
	private ?Types\Log $type = NULL;
	private ?Types\Column         $table_column = NULL;

	private ?int    $table_id = NULL;
	private ?string $notes    = NULL;

	/**
	 * @param null|TravelAffiliateMembers\Log $log
	 * @param TravelAffiliateMembership       $member
	 * @param Requests\Log $action
	 */
	public function __construct(?TravelAffiliateMembers\Log $log, TravelAffiliateMembership $member, Requests\Log $action = Requests\Log::ADD) {
		$this->log    = $log;
		$this->member = $member;
		$this->action = $action;
	}

	/**
	 * @param TravelAffiliateMembership $member
	 *
	 * @return $this
	 */
	public static function Init(TravelAffiliateMembership $member): self {
		return new self(NULL, $member);
	}

	/**
	 * @param Requests\Log $action
	 *
	 * @return $this
	 */
	public function setAction(Requests\Log $action): self {
		$this->action = $action;
		return $this;
	}

	/**
	 * @param Types\Log $type
	 *
	 * @return Log
	 */
	public function setType(Types\Log $type): Log {
		$this->type = $type;
		return $this;
	}

	/**
	 * @param Types\Log                 $type
	 * @param null|Interfaces\TableEnum $table_name
	 * @param null|int                  $table_id
	 * @param null|Types\Column         $table_column
	 * @param null|string               $filename
	 * @param null|string               $notes
	 *
	 * @return $this
	 */
	public function setData(Types\Log $type, ?Interfaces\TableEnum $table_name = NULL, ?int $table_id = NULL, ?Types\Column $table_column = NULL, ?string $notes = NULL): self {
		$this->type         = $type;
		$this->table_name   = $table_name;
		$this->table_id     = $table_id;
		$this->table_column = $table_column;
		$this->notes        = $notes;
		return $this;
	}

	/**
	 * @param null|Interfaces\TableEnum $table_name
	 *
	 * @return $this
	 */
	public function setTableName(?Interfaces\TableEnum $table_name): self {
		$this->table_name = $table_name;
		return $this;
	}

	/**
	 * @param int $table_id
	 *
	 * @return $this
	 */
	public function setTableId(int $table_id): self {
		$this->table_id = $table_id;
		return $this;
	}

	/**
	 * @param Types\Column $table_column
	 *
	 * @return $this
	 */
	public function setTableColumn(Types\Column $table_column): self {
		$this->table_column = $table_column;
		return $this;
	}

	/**
	 * @param null|string $notes
	 *
	 * @return $this
	 */
	public function setNotes(?string $notes): self {
		$this->notes = $notes;
		return $this;
	}

	/**
	 * @return bool
	 *
	 * @throws Error|PDOException|UnhandledMatchError
	 */
	public function execute(): bool {
		return match ($this->getAction()) {
			Requests\Log::ADD    => $this->add(),
			Requests\Log::REMOVE => $this->remove()
		};
	}

	/**
	 * @return bool
	 *
	 * @throws PDOException
	 */
	private function add(): bool {
		return Database::Action(
			"INSERT INTO `travel_affiliate_member_logs` SET `type` = :type, `member_id` = :member_id, `table_name` = :table_name, `table_id` = :table_id, `table_column` = :table_column, `user_agent` = :user_agent, `ip_address` = :ip_address",
			array(
				'type'         => $this->getType()?->getValue(),
				'member_id'    => $this->getMember()->getId(),
				'table_name'   => $this->getTableName()?->getValue(),
				'table_id'     => $this->getTableId(),
				'table_column' => $this->getTableColumn()?->getValue(),
				'user_agent'   => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'   => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			)
		)->rowCount();
	}

	/**
	 * @return bool
	 *
	 * @throws PDOException
	 */
	private function remove(): bool {
		return Database::Action("DELETE FROM `travel_affiliate_member_logs` WHERE `id` = :id AND `member_id` = :member_id", array(
			'id'        => $this->getLog()?->getId(),
			'member_id' => $this->getMember()->getId()
		))->rowCount();
	}

	/**
	 * @return null|Types\Log
	 */
	private function getType(): ?Types\Log {
		return $this->type ?? $this->getLog()?->getType();
	}

	/**
	 * @return null|TravelAffiliateMembers\Log
	 */
	private function getLog(): ?TravelAffiliateMembers\Log {
		return $this->log;
	}

	/**
	 * @return TravelAffiliateMembership
	 */
	private function getMember(): TravelAffiliateMembership {
		return $this->member;
	}

	/**
	 * @return null|Interfaces\TableEnum
	 */
	private function getTableName(): ?Interfaces\TableEnum {
		return $this->table_name;
	}

	/**
	 * @return null|int
	 */
	private function getTableId(): ?int {
		return $this->table_id;
	}

	/**
	 * @return null|Types\Column
	 */
	private function getTableColumn(): ?Types\Column {
		return $this->table_column;
	}

	/**
	 * @return null|string
	 */
	private function getNotes(): ?string {
		return $this->notes;
	}

	/**
	 * @return Requests\Log
	 */
	private function getAction(): Requests\Log {
		return $this->action;
	}
}
