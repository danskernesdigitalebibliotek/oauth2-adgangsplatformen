<?php

namespace Adgangsplatformen\Provider;

use PHPUnit\Framework\TestCase;

class AdgangsplatformenUserTest extends TestCase
{

    public function testUserAttributes()
    {
        $uniqueId = 'abcd1234';
        $municipalityNumber = 123;
        $cpr = '010203791234';
        $userId = $cpr;
        $userIdType = 'CPR';
        $agencyId = '710100';
        $userArray = [
            'attributes' => [
                'uniqueId' => $uniqueId,
                'municipality' => $municipalityNumber,
                'cpr' => $cpr,
                'userId' => $userId,
                'libraries' => [
                    [
                        'agencyId' => $agencyId,
                        'userId' => $userId,
                        'userIdType' => $userIdType
                    ]
                ]
            ],
        ];
        $user = new AdgangsplatformenUser($userArray);

        $this->assertEquals($uniqueId, $user->getId());
        $this->assertEquals($municipalityNumber, $user->getMunicipalityNumber());
        $this->assertEquals($cpr, $user->getCPR());
        $this->assertEquals($userId, $user->getUserId());
        $this->assertEquals(
            [ new AdgangsplatformenUserLibrary($agencyId, $userId, $userIdType) ],
            $user->getLibraries()
        );

        $this->assertEquals($userArray, $user->toArray());
    }

    public function testMissingAttributes()
    {
        $user = new AdgangsplatformenUser([
           'attributes' => [
             'uniqueId' => 'abcd1234',
             'municipality' => 123
           ],
        ]);

        $this->assertNull($user->getCPR());
        $this->assertNull($user->getUserId());
        $this->assertEquals([], $user->getLibraries());
    }

    public function testMissingLibraryAttributes()
    {
        $user = new AdgangsplatformenUser([
            'attributes' => [
                'uniqueId' => 'abcd1234',
                'municipality' => 123,
                'libraries' => [
                    [
                        'agencyId' => '710100',
                        'userId' => '010203791234'
                    ]
                ]
            ],
        ]);

        $this->assertEquals([], $user->getLibraries());
    }
}
