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

interface TravelAffiliateMember extends Item {
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
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @return null|string
     */
    public function getPhone(): ?string;

    /**
     * @return string
     */
    public function getTravelAgency(): string;

    /**
     * @return string
     */
    public function getTravelAgencyEinNumber(): string;

    /**
     * @return string
     */
    public function getNotes(): string;

    /**
     * @return float
     */
    public function getTicketCommisionRate(): float;

    /**
     * @return float
     */
    public function getRoomCommisionRate(): float;

    /**
     *
     * @return array
     */
    public function getAdminCommissionNote(): array;

    /**
     * @return bool
     */
    public function isApproved(): bool;

    /**
     * @return bool
     */
    public function isBanned(): bool;

    /**
     * @return null|string
     */
    public function getTermsPrivacySignature(): ?string;

    /**
     * @return null|string
     */
    public function getAffiliateTermsConditionsSignature(): ?string;

    /**
     * @return null|string
     */
    public function getAdminApprovalSignature(): ?string;

    /**
     * @return bool
     */
    public function isVerified(): bool;
}
