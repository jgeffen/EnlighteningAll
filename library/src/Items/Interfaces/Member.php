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

namespace Items\Interfaces;

use Items\Enums\Options;

interface Member extends Item {
	/**
	 * @return string
	 */
	public function getUsername(): string;

	/**
	 * @return string
	 */
	public function getEmail(): string;

	/**
	 * @return string
	 */
	public function getPasswordHash(): string;

	/**
	 * @return null|string
	 */
	public function getAddressLine1(): ?string;

	/**
	 * @return null|string
	 */
	public function getAddressLine2(): ?string;

	/**
	 * @return null|string
	 */
	public function getAddressCity(): ?string;

	/**
	 * @return null|string
	 */
	public function getAddressCountry(): ?string;

	/**
	 * @return null|string
	 */
	public function getAddressState(): ?string;

	/**
	 * @return null|string
	 */
	public function getAddressZipCode(): ?string;

	/**
	 * @return array
	 */
	public function getBeadColors(): array;

	/**
	 * @return null|string
	 */
	public function getBio(): ?string;

	/**
	 * @return string
	 */
	public function getFirstName(): string;

	/**
	 * @return string
	 */
	public function getFirstNames(): string;

	/**
	 * @return string
	 */
	public function getFullName(): string;

	/**
	 * @return string
	 */
	public function getFullNameLast(): string;

	/**
	 * @return string
	 */
	public function getLastName(): string;

	/**
	 * @return null|Options\NecklaceColors
	 */
	public function getNecklaceColor(): ?Options\NecklaceColors;

	/**
	 * @return Options\BeadColors[]
	 */
	public function getPartnerBeadColors(): array;

	/**
	 * @return null|string
	 */
	public function getPartnerFirstName(): ?string;

	/**
	 * @return null|Options\NecklaceColors
	 */
	public function getPartnerNecklaceColor(): ?Options\NecklaceColors;

	/**
	 * @return null|string
	 */
	public function getPhone(): ?string;

	/**
	 * @return bool
	 */
	public function isApproved(): bool;

	/**
	 * @return bool
	 */
	public function isBanned(): bool;

	/**
	 * @return bool
	 */
	public function isCouple(): bool;

	/**
	 * @return bool
	 */
	public function isVerified(): bool;

	/**
	 * @return bool
	 */
	public function isIdVerified(): bool;

	/**
	 * @return null|string
	 */
	public function getIdVerifiedAdminApproval(): ?string;


	/**
	 * @return null|string
	 */
	public function getIdVerifiedTimestamp(): ?string;

	/**
	 * @return null|string
	 */
	public function getIdVerifiedIpAddress(): ?string;
	/**
	 * @return string
	 */
	public function getLink(): string;
}
