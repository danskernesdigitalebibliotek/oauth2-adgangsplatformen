<?php

namespace Adgangsplatformen\Provider;

class AdgangsplatformenUserLibrary
{

    /* @var string */
    private $agencyId;

    /* @var string */
    private $userId;

    /* @var string */
    private $userIdType;

    public function __construct(string $agencyId, string $userId, string $userIdType)
    {
        $this->agencyId = $agencyId;
        $this->userId = $userId;
        $this->userIdType = $userIdType;
    }

    /**
     * Returns the agency code of the library organization.
     *
     * This is the same as the ISIL number (ISO 15511) for the library but
     * without "DK-" prefixed.
     *
     * @see https://vip.dbc.dk/lister.php?vis=folk
     */
    public function getAgencyId(): string
    {
        return $this->agencyId;
    }

    /**
     * Returns the type of user id representing the user.
     *
     * Known values:
     * - CPR: A CPR number.
     * - LOCAL: An identifier used by the local library e.g. library card number.
     */
    public function getUserIdType(): string
    {
        return $this->userIdType;
    }

    /**
     * Returns the user id.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }
}
