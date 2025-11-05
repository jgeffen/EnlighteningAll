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

namespace Items\TravelAffiliateMembers;

use Database;
use Items\Interfaces;
use Items\TravelAffiliateMember;
use Items\Traits;
use PDO;
use PDOStatement;

class Polling implements Interfaces\Item {
    use Traits\Item;

    private ?TravelAffiliateMember $member;

    protected int $member_id;

    /**
     * @param null|int $id
     *
     * @return null|$this
     */
    public static function Init(?int $id): ?self {
        return Database::Action("SELECT * FROM `travel_affiliate_member_polling` WHERE `id` = :id", array(
            'id' => $id
        ))->fetchObject(self::class) ?: NULL;
    }

    /**
     * @param PDOStatement $statement
     *
     * @return null|static
     */
    public static function Fetch(PDOStatement $statement): ?self {
        return $statement->fetchObject(self::class) ?: NULL;
    }

    /**
     * @param PDOStatement $statement
     *
     * @return self[]
     */
    public static function FetchAll(PDOStatement $statement): array {
        return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * @return int
     */
    public function getMemberId(): int {
        return $this->member_id;
    }

    /**
     * @return null|TravelAffiliateMember
     */
    public function getMember(): ?TravelAffiliateMember {
        !isset($this->member) && $this->setMember();
        return $this->member;
    }

    /**
     * @return void
     */
    public function setMember(): void {
        $this->member = TravelAffiliateMember::Init($this->getMemberId());
    }
}
