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

namespace Items;

use Database;
use Exception;
use Items\Enums\Statuses;
use PDO;
use PDOStatement;

class TravelAffiliateMember extends Abstracts\TravelAffiliateMember {

    /**
     * @param null|int $id
     *
     * @return null|$this
     */
    public static function Init(?int $id): ?self {
        return Database::Action("SELECT * FROM `travel_affiliate_members` WHERE `id` = :id", array(
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
}
