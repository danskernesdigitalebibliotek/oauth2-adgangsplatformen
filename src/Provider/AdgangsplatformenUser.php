<?php

declare(strict_types=1);

namespace Adgangsplatformen\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class AdgangsplatformenUser implements ResourceOwnerInterface
{

    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Returns an id for the user which is anonymized and globally unique.
     *
     * All users should have have a GUID but practice shows that this is not
     * always the case.
     */
    public function getId(): ?string
    {
        return $this->response['attributes']['uniqueId'] ?? null;
    }

    /**
     * Returns the municipality number for the user.
     *
     * @see http://www.linking.dk/lokalportaler/kommuner.html
     */
    public function getMunicipalityNumber(): int
    {
        return $this->response['attributes']['municipality'];
    }

    /**
     * Returns the user ID used at login.
     *
     * Normally thus corresponds with CPR, but this is not guarantied.
     */
    public function getUserId(): ?string
    {
        return $this->response['attributes']['userId'] ?? null;
    }

    /**
     * Returns the CPR number of the user.
     */
    public function getCPR(): ?string
    {
        return $this->response['attributes']['cpr'] ?? null;
    }

    /**
     * Returns a list of libraries the user is registered on.
     *
     * @return \Adgangsplatformen\Provider\AdgangsplatformenUserLibrary[]
     */
    public function getLibraries(): array
    {
        return array_filter(array_map(function (array $data) {
            try {
                return new AdgangsplatformenUserLibrary(
                    $data['agencyId'],
                    $data['userId'],
                    $data['userIdType']
                );
            } catch (\Throwable $e) {
                // Do not include libraries with missing data.
                return null;
            }
        }, $this->response['attributes']['libraries'] ?? []));
    }

    /**
     * Return all of the owner details available as an array.
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
