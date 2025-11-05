<?php
/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
*/

/** @noinspection PhpUnused */

namespace Items\Members\Types;

use DateTime;
use Items\Abstracts;
use PDO;
use PDOStatement;

class Friend extends Abstracts\Member {
    private int    $member_1;
    private int    $member_2;
    private string $friend_since;

    /* -------------------------------------------------------------------------
     * Optional fields often returned by JOINs (prevent “Unhandled property” errors)
     * ---------------------------------------------------------------------- */
    public ?string $status     = null;
    public ?string $created_at = null;
    public ?string $role       = null;

    public ?int $member_id = null;
    public ?int $initiated_by = null;
    public ?string $confirmation_questions = null;
    public ?string $confirmation_answer = null;


    /**
     * @param PDOStatement $statement
     *
     * @return null|static
     */
    public static function Fetch(PDOStatement $statement): ?static {
        return $statement->fetchObject(static::class) ?: NULL;
    }

    /**
     * @param PDOStatement $statement
     *
     * @return static[]
     */
    public static function FetchAll(PDOStatement $statement): array {
        return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    /**
     * @return int
     */
    public function getMember1(): int {
        return $this->member_1;
    }

    /**
     * @return int
     */
    public function getMember2(): int {
        return $this->member_2;
    }

    /**
     * @return DateTime
     */
    public function getFriendSince(): DateTime {
        return date_create($this->friend_since);
    }

    /**
     * @return string
     */
    public function getSortJson(): string {
        return htmlspecialchars(json_encode(array(
            'first_names' => $this->getFirstNames(),
            'partner_1'   => $this->getFirstName(),
            'partner_2'   => $this->getPartnerFirstName(),
            'avatar'      => (bool)$this->getAvatar(),
            'last_online' => $this->getLastOnline()?->getTimestamp()
        ), JSON_ERROR_NONE), ENT_COMPAT);
    }

    /**
     * Magic setter to prevent “Unhandled property” errors from PDO hydration.
     */
    public function __set(string $name, $value): void {
        $this->$name = $value;
    }
}
