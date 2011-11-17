<?php

require_once dirname(__FILE__) . '/../ExpiringHash.php';

class ExpiringHashest extends PHPUnit_Framework_TestCase
{
    function generateHashDataProvider()
    {
        return array(
            array('2011-01-01 12:00:00', '2011-01-01T12:00:00-05:00.692419ae8dfb985bcab182e309f8b090cf17cecb7cc3a10a32d39ad7e048c68a')
        );
    }
    /**
     * @dataProvider generateHashDataProvider
     */
    function testGenerateHash($expiry, $expectedOutput)
    {
        $hash = ExpiringHash::create('secret')
            ->generate($expiry)
            ;
        $this->assertEquals($expectedOutput, $hash);
    }

    function validateHashDataProvider()
    {
        return array(
            // the exact time
            array('2011-01-01T12:00:00-05:00.692419ae8dfb985bcab182e309f8b090cf17cecb7cc3a10a32d39ad7e048c68a', '2011-01-01T12:00:00-05:00', ExpiringHash::STATUS_OK),
            // one second early
            array('2011-01-01T12:00:00-05:00.692419ae8dfb985bcab182e309f8b090cf17cecb7cc3a10a32d39ad7e048c68a', '2011-01-01T11:59:59-05:00', ExpiringHash::STATUS_OK),
            // one second too late
            array('2011-01-01T12:00:00-05:00.692419ae8dfb985bcab182e309f8b090cf17cecb7cc3a10a32d39ad7e048c68a', '2011-01-01T12:00:01-05:00', ExpiringHash::STATUS_EXPIRED),
            // span time zone; expired
            array('2011-01-01T12:00:00-05:00.692419ae8dfb985bcab182e309f8b090cf17cecb7cc3a10a32d39ad7e048c68a', '2011-01-01T12:00:00-06:00', ExpiringHash::STATUS_EXPIRED),
            // span time zone; favorable
            array('2011-01-01T12:00:00-05:00.692419ae8dfb985bcab182e309f8b090cf17cecb7cc3a10a32d39ad7e048c68a', '2011-01-01T12:00:00-04:00', ExpiringHash::STATUS_OK),
            // tamper date
            array('2011-01-02T12:00:00-05:00.692419ae8dfb985bcab182e309f8b090cf17cecb7cc3a10a32d39ad7e048c68a', '2011-01-01T12:00:00-05:00', ExpiringHash::STATUS_TAMPERED),
            // tamper hash
            array('2011-01-01T12:00:00-05:00.692419ae8dfb985bcab182e309f8b090cf17cecb7cc3a10a32d39ad7e048c68b', '2011-01-01T12:00:00-05:00', ExpiringHash::STATUS_TAMPERED),
        );
    }
    /**
     * @dataProvider validateHashDataProvider
     */
    function testValidateHash($hash, $now, $expectedResult)
    {
        $result = ExpiringHash::create('secret')
            ->validate($hash, $now)
            ;
        $this->assertEquals($expectedResult, $result);
    }

    function testGenerateAndValidateAgainstNow()
    {
        $eh = new ExpiringHash('secret');

        $hash = $eh->generate("2 seconds");
        $this->assertEquals(ExpiringHash::STATUS_OK, $eh->validate($hash));

        $hash = $eh->generate("-1 seconds");
        $this->assertEquals(ExpiringHash::STATUS_EXPIRED, $eh->validate($hash));
    }
}
