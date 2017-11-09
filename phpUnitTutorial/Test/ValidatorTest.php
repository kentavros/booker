<?php
require_once '../../server/app/lib/config.php';
require_once 'Db.php';
require_once '../../server/app/models/ModelDB.php';
require_once '../../server/app/lib/Validator.php';
class ValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $valid;

    protected function setUp()
    {
        $this->valid = new Validator();
    }
    protected function tearDown()
    {
        $this->valid = NULL;
    }

    public function testIsNoWeekendTrue()
    {
        $dateS = new DateTime();
        $dateS->setTime(TIME_START, 0);
        $dateS->modify('+1 day');
        if (date("w", $dateS->getTimestamp()) == 0 || date("w", $dateS->getTimestamp()) == 6)
        {
            $dateS->modify('+2 day');
        }
        $start = $dateS->getTimestamp()*1000;
        $result = $this->valid->isNoWeekend($start);
        $this->assertTrue($result);
    }

    public function testIsNoWeekendFalse()
    {
        $dateS = new DateTime();
        $dateS->setTime(TIME_START, 0);
        $dateS->modify('+1 day');
        while (true)
        {
            if(date("w", $dateS->getTimestamp()) == 0 || date("w", $dateS->getTimestamp()) == 6)
            {
                break;
            }
            $dateS->modify('+1 day');
        }
        $start = $dateS->getTimestamp()*1000;
        $result = $this->valid->isNoWeekend($start);
        $this->assertFalse($result);
    }

    public function testIsValidateRegistrationTrue()
    {
        $result = $this->valid->isValidateRegistration([
            'username' => 'test4884',
            'login' => 'test4884',
            'email' => 'test4884@email.com',
            'pass' => 'test4884',
            'id_role' => 'admin'
        ]);
        $this->assertTrue($result);
    }

    public function testIsValidateRegistrationFalse_Fields()
    {
        $result = $this->valid->isValidateRegistration([
            'username' => 'test4884',
            'login' => 'test4884',
            'email' => 'test4884@email.com',
            'pass' => 'test4884',
        ]);
        $this->assertEquals(ERR_FIELDS, $result);
    }

    public function testIsValidateRegistrationFalseUserName()
    {
        $result = $this->valid->isValidateRegistration([
            'username' => '/',
            'login' => 'test4884',
            'email' => 'test4884@email.com',
            'pass' => 'test4884',
            'id_role' => 'admin'
        ]);
        $this->assertEquals(INVAL_USERNAME, $result);
    }

    public function testIsValidateRegistrationLogin()
    {
        $result = $this->valid->isValidateRegistration([
            'username' => 'test4884',
            'login' => '@@@@@',
            'email' => 'test4884@email.com',
            'pass' => 'test4884',
            'id_role' => 'admin'
        ]);
        $this->assertEquals(INVAL_LOGIN, $result);
    }

    public function testIsValidateRegistrationEmail()
    {
        $result = $this->valid->isValidateRegistration([
            'username' => 'test4884',
            'login' => 'test4884',
            'email' => 'test4884@email',
            'pass' => 'test4884',
            'id_role' => 'admin'
        ]);
        $this->assertEquals(INVAL_EMAIL, $result);
    }

    public function testIsValidateRegistrationPassword()
    {
        $result = $this->valid->isValidateRegistration([
            'username' => 'test4884',
            'login' => 'test4884',
            'email' => 'test4884@email.com',
            'pass' => '@@##3#',
            'id_role' => 'admin'
        ]);
        $this->assertEquals(INVAL_PASS, $result);
    }

    public function testIsValidateEditTrueWithoutPass()
    {
        $result=$this->valid->isValidateEdit([
            'username' => 'test4884',
            'email' => 'test4884@email.com',
            'role' => 'admin'
        ]);
        $this->assertTrue($result);
    }

    public function testIsValidateEditTrueWithPass()
    {
        $result=$this->valid->isValidateEdit([
            'username' => 'test4884',
            'email' => 'test4884@email.com',
            'role' => 'admin',
            'pass' => 'test4884',
        ]);
        $this->assertTrue($result);
    }

    public function testIsValidEventAdd()
    {
        $dateS = new DateTime();
        $dateS->setTime(TIME_START, 0);
        $dateS->modify('+1 day');
        if (date("w", $dateS->getTimestamp()) == 0 || date("w", $dateS->getTimestamp()) == 6)
        {
            $dateS->modify('+2 day');
        }
        $start = $dateS->getTimestamp()*1000;
        $dateE = new DateTime($dateS->format(TIME_FORMAT));
        $dateE->modify('+1 hour');
        $end = $dateE->getTimestamp()*1000;
        $result = $this->valid->isValidEventAdd([
            'booked_for' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'description' => 'test4884'
        ]);
        $this->assertTrue($result);
    }

    public function testIsValidEventAddErrorFields()
    {
        $start = 1;
        $end = 2;
        $result = $this->valid->isValidEventAdd([
            'booked_for' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
//            'description' => 'test4884'
        ]);
        $this->assertEquals(ERR_FIELDS, $result);
    }

    public function testIsValidEventAddErrorDescription()
    {
        $start = 1;
        $end = 2;
        $result = $this->valid->isValidEventAdd([
            'booked_for' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'description' => 'tes'
        ]);
        $this->assertEquals(INVAL_DESCR, $result);
    }

    public function testIsValidEventAddErrorIsTimeStartNoMoreTimeEnd()
    {
        $dateS = new DateTime();
        $dateS->setTime(TIME_START, 0);
        $dateS->modify('+1 day');
        if (date("w", $dateS->getTimestamp()) == 0 || date("w", $dateS->getTimestamp()) == 6)
        {
            $dateS->modify('+2 day');
        }
        $start = $dateS->getTimestamp()*1000;
        $dateE = new DateTime($dateS->format(TIME_FORMAT));
        $dateE->modify('-3 hour');
        $end = $dateE->getTimestamp()*1000;
        $result = $this->valid->isValidEventAdd([
            'booked_for' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'description' => 'test4884'
        ]);
        $this->assertEquals(INVAL_TIMEMORE, $result);
    }

    public function testIsValidEventAddErrorisValidTimeStEn()
    {
        $dateS = new DateTime();
        $dateS->setTime(TIME_START-4, 0);
        $dateS->modify('+1 day');
        if (date("w", $dateS->getTimestamp()) == 0 || date("w", $dateS->getTimestamp()) == 6)
        {
            $dateS->modify('+2 day');
        }
        $start = $dateS->getTimestamp()*1000;
        $dateE = new DateTime($dateS->format(TIME_FORMAT));
        $dateE->modify('+3 hour');
        $end = $dateE->getTimestamp()*1000;
        $result = $this->valid->isValidEventAdd([
            'booked_for' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'description' => 'test4884'
        ]);
        $this->assertEquals(INVAL_TIME_S_E, $result);
    }

    public function testIsValidEventAddErrorInvalRecurringMethod()
    {
        $dateS = new DateTime();
        $dateS->setTime(TIME_START, 0);
        $dateS->modify('+1 day');
        if (date("w", $dateS->getTimestamp()) == 0 || date("w", $dateS->getTimestamp()) == 6)
        {
            $dateS->modify('+2 day');
        }
        $start = $dateS->getTimestamp()*1000;
        $dateE = new DateTime($dateS->format(TIME_FORMAT));
        $dateE->modify('+3 hour');
        $end = $dateE->getTimestamp()*1000;
        $result = $this->valid->isValidEventAdd([
            'booked_for' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'description' => 'test4884',
            'recurringMethod' => 'test'
        ]);
        $this->assertEquals(INVAL_RECURR, $result);
    }



}