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

namespace Items\TravelAffiliateMembers\Actions;

use Database;
use Error;
use Items;
use Items\Enums\Requests;
use Items\Enums\Tables;
use Items\Enums\Types;
use Items\Interfaces;
use JetBrains\PhpStorm\Pure;
use TravelAffiliateMembership;
use PDOException;
use UnhandledMatchError;

class Account implements Interfaces\Action {
	private TravelAffiliateMembership $member;
	private Requests\Account $action;
	private Types\Column     $column;

	private null|int|string $value;

	/**
	 * @param TravelAffiliateMembership $member
	 */
	public function __construct(TravelAffiliateMembership $member) {
		$this->member = $member;
	}

	/**
	 * @param TravelAffiliateMembership $member
	 *
	 * @return $this
	 */
	public static function Init(TravelAffiliateMembership $member): self {
		return new self($member);
	}

	/**
	 * @param Requests\Account $action
	 *
	 * @return $this
	 */
	public function setAction(Requests\Account $action): self {
		$this->action = $action;
		return $this;
	}

	/**
	 * @param Types\Column $column
	 *
	 * @return Account
	 */
	public function setColumn(Types\Column $column): Account {
		$this->column = $column;
		return $this;
	}

	/**
	 * @param null|int|string $value
	 *
	 * @return Account
	 */
	public function setValue(int|string|null $value): Account {
		$this->value = $value;
		return $this;
	}

	/**
	 * @return bool
	 *
	 * @throws Error|PDOException|UnhandledMatchError
	 */
	public function execute(): bool {
		return match ($this->getAction()) {
			Requests\Account::UPDATE => $this->update(),
			Requests\Account::VERIFY => $this->verify()
		};
	}

	/**
	 * @return bool
	 *
	 * @throws PDOException
	 */
	private function update(): bool {
		$this->getMember()->log()->setData(
			type: Types\Log::UPDATE,
			table_name: Tables\Secrets::TRAVEL_AFFILIATE_MEMBERS,
			table_id: $this->getMember()->getId(),
			table_column: $this->getColumn()
		)->execute();

		return match ($this->getColumn()) {
			Types\Column::PASSWORD => Database::Action("UPDATE `travel_affiliate_members` SET `password` = :password WHERE `id` = :identifier", array(
				'password'   => password_hash($this->getValue(), PASSWORD_DEFAULT),
				'identifier' => $this->getMember()->getId()
			))->rowCount(),
			default                => FALSE
		};
	}

	/**
	 * @return bool
	 *
	 * @throws PDOException
	 */
	private function verify(): bool {
		$this->getMember()->log()->setData(
			type: Types\Log::VERIFY,
			table_name: Tables\Secrets::TRAVEL_AFFILIATE_MEMBERS,
			table_id: $this->getMember()->getId()
		)->execute();

		return Database::Action("UPDATE `travel_affiliate_members` SET `verified` = :verified WHERE `email` = :email", array(
			'email'    => $this->getMember()->getEmail(),
			'verified' => TRUE
		))->rowCount();
	}

	/**
	 * @return TravelAffiliateMembership
	 */
	private function getMember(): TravelAffiliateMembership {
		return $this->member;
	}

	/**
	 * @return Requests\Account
	 */
	private function getAction(): Requests\Account {
		return $this->action;
	}

	/**
	 * @return Types\Column
	 */
	private function getColumn(): Types\Column {
		return $this->column;
	}

	/**
	 * @return null|int|string
	 */
	private function getValue(): int|string|null {
		return $this->value;
	}
}
