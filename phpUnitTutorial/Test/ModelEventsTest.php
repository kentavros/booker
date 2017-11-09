<?php
require_once '../../server/app/lib/config.php';
require_once 'Db.php';
require_once '../../server/app/models/ModelDB.php';
require_once '../../server/app/lib/Validator.php';
require_once '../../server/app/models/ModelEvents.php';
class ModelEventsTest extends PHPUnit_Framework_TestCase
{
    protected $modelEvents;
    protected $pdo;

    protected function setUp()
    {
        $this->modelEvents = new ModelEvents();
        $this->pdo = new Db();
    }
    protected function tearDown()
    {
        $this->modelEvents = NULL;
        $this->pdo = NULL;
    }

    public function testGetEventsByRequestMonth_DataArray()
    {
        $date = new DateTime();
        $requDate = $date->format('Y-m');
        $dateS = new DateTime();
        $dateS->modify('+1 day');
        $start = $dateS->format(TIME_FORMAT);
        $dateE = new DateTime();
        $dateE->modify('+1 day');
        $dateE->modify('+1 hour');
        $end = $dateE->format(TIME_FORMAT);
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start.'", "'.$end.'", "2")');
        $id_event = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelEvents->getEvents([
            'hash' => 'test',
            'id_user' => $id_user,
            'flag' => 'like',
            'time_start' => $requDate
        ]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) > 0);
        $this->pdo->execQuery('DELETE FROM events WHERE id='.$id_event);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testGetEventsByIdOrIdParent()
    {
        $dateS = new DateTime();
        $dateS->modify('+1 day');
        $start = $dateS->format(TIME_FORMAT);
        $dateE = new DateTime();
        $dateE->modify('+1 day');
        $dateE->modify('+1 hour');
        $end = $dateE->format(TIME_FORMAT);
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start.'", "'.$end.'", "2")');
        $id_event = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelEvents->getEvents([
            'hash' => 'test',
            'id_user' => $id_user,
            'flag' => 'parent',
            'id' => $id_event,
            'event_id_user' => $id_user
        ]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) > 0);
        $this->pdo->execQuery('DELETE FROM events WHERE id='.$id_event);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testGetEventsAccessDenied()
    {
        $result = $this->modelEvents->getEvents([
            'hash' => 'test',
            'id_user' => 'test',
        ]);
        $this->assertEquals(ERR_ACCESS, $result);
    }

    public function testGetEventsFalse()
    {

        $dateS = new DateTime();
        $dateS->modify('+1 day');
        $start = $dateS->format(TIME_FORMAT);
        $dateE = new DateTime();
        $dateE->modify('+1 day');
        $dateE->modify('+1 hour');
        $end = $dateE->format(TIME_FORMAT);
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelEvents->getEvents([
            'hash' => 'test',
            'id_user' => $id_user,
            'flag' => 'like',
        ]);
        $this->assertFalse($result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testAddEventsOneEventAdd()
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
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelEvents->addEvents([
            'hash' => 'test',
            'id_user' => $id_user,
            'booked_for' => $id_user,
            'id_room' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'description' => 'test',
        ]);
        $this->assertEquals(1, $result);
        $this->pdo->execQuery('DELETE FROM events WHERE id_user='.$id_user);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testAddEventsWeeklyFour()
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
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelEvents->addEvents([
            'hash' => 'test',
            'id_user' => $id_user,
            'booked_for' => $id_user,
            'id_room' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'description' => 'test',
            'recurringMethod' => 'weekly',
            'duration' => '4'
        ]);
        $this->assertTrue($result);
        $this->pdo->execQuery('DELETE FROM events WHERE id_user='.$id_user);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testAddEventsErrorAddEvent()
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
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$dateS->format(TIME_FORMAT).'", "'.$dateE->format(TIME_FORMAT).'", "2")');
        $result = $this->modelEvents->addEvents([
            'hash' => 'test',
            'id_user' => $id_user,
            'booked_for' => $id_user,
            'id_room' => '1',
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'description' => 'test',
        ]);
        $this->assertEquals(ERR_ADDEVENT, $result);
        $this->pdo->execQuery('DELETE FROM events WHERE id_user='.$id_user);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testAddEventsErrorValidate()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelEvents->addEvents([
            'hash' => 'test',
            'id_user' => $id_user,
        ]);
        $this->assertEquals(ERR_FIELDS, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testAddEventsAccessDenied()
    {
        $result = $this->modelEvents->addEvents([
            'hash' => 'test',
            'id_user' => '1',
        ]);
        $this->assertEquals(ERR_ACCESS, $result);
    }

    public function testDeleteEventTrue()
    {
        $dateS = new DateTime();
        $dateS->modify('+1 day');
        $start = $dateS->format(TIME_FORMAT);
        $dateE = new DateTime();
        $dateE->modify('+1 day');
        $dateE->modify('+1 hour');
        $end = $dateE->format(TIME_FORMAT);
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start.'", "'.$end.'", "2")');
        $id_event = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelEvents->deleteEvent([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_event
        ]);
        $this->assertEquals(1, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testDeleteEventErrorAccess()
    {
        $result = $this->modelEvents->deleteEvent([
            'hash' => 'test',
            'id_user' => '1',
        ]);
        $this->assertEquals(ERR_ACCESS, $result);
    }

    public function testDeleteEventTrueRecurringTwoChailde()
    {
        $dateS = new DateTime();
        $dateS->modify('+1 day');
        $start = $dateS->format(TIME_FORMAT);
        $dateE = new DateTime();
        $dateE->modify('+1 day');
        $dateE->modify('+1 hour');
        $end = $dateE->format(TIME_FORMAT);
        $dateS->modify('+1 day');
        $start2 = $dateS->format(TIME_FORMAT);
        $dateE->modify('+1 day');
        $end2 = $dateE->format(TIME_FORMAT);
        $dateS->modify('+1 day');
        $start3 = $dateS->format(TIME_FORMAT);
        $dateE->modify('+1 day');
        $end3 = $dateE->format(TIME_FORMAT);
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start.'", "'.$end.'")');
        $id_event = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start2.'", "'.$end2.'", "'.$id_event.'")');
        $id_event2 = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start3.'", "'.$end3.'", "'.$id_event.'")');
        $result = $this->modelEvents->deleteEvent([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_event2,
            'checked' => true,
            'id_parent' => $id_event,
            'time_start' => $start2,
            'event_id_user'=> $id_user

        ]);
        $this->assertEquals(2, $result);
        $this->pdo->execQuery('DELETE FROM events WHERE id='.$id_event);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testDeleteEventTrueRecurringParentAndChilde()
    {
        $dateS = new DateTime();
        $dateS->modify('+1 day');
        $start = $dateS->format(TIME_FORMAT);
        $dateE = new DateTime();
        $dateE->modify('+1 day');
        $dateE->modify('+1 hour');
        $end = $dateE->format(TIME_FORMAT);
        $dateS->modify('+1 day');
        $start2 = $dateS->format(TIME_FORMAT);
        $dateE->modify('+1 day');
        $end2 = $dateE->format(TIME_FORMAT);
        $dateS->modify('+1 day');
        $start3 = $dateS->format(TIME_FORMAT);
        $dateE->modify('+1 day');
        $end3 = $dateE->format(TIME_FORMAT);
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start.'", "'.$end.'")');
        $id_event = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start2.'", "'.$end2.'", "'.$id_event.'")');
        $id_event2 = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start3.'", "'.$end3.'", "'.$id_event.'")');
        $result = $this->modelEvents->deleteEvent([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_event,
            'checked' => true,
            'id_parent' => 'null',
            'event_id_user'=> $id_user

        ]);
        $this->assertEquals(3, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testEditEventUpdateOneEvent()
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

        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$dateS->format(TIME_FORMAT).'", "'.$dateE->format(TIME_FORMAT).'", "2")');
        $id_event = $this->pdo->getPdo()->lastInsertId();

        $result = $this->modelEvents->editEvent([
            'hash' => 'test',
            'id_user' => $id_user,
            'booked_for' => $id_user,
            'event_id' => $id_event,
            'dateTimeStart' => $start,
            'dateTimeEnd' => $end,
            'id_room' => '1',
            'description' => 'testUpdate'
        ]);
        $this->assertEquals(1, $result);
        $this->pdo->execQuery('DELETE FROM events WHERE id='.$id_event);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testEditEventAccessDenied()
    {
        $result = $this->modelEvents->editEvent([
            'hash' => 'test',
            'id_user' => '1',
        ]);
        $this->assertEquals(ERR_ACCESS, $result);
    }

    public function testEditEventUpdateReccuring()
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

        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$dateS->format(TIME_FORMAT).'", "'.$dateE->format(TIME_FORMAT).'")');
        $id_event = $this->pdo->getPdo()->lastInsertId();
        $dateS->modify('+1 day');
        if (date("w", $dateS->getTimestamp()) == 0 || date("w", $dateS->getTimestamp()) == 6)
        {
            $dateS->modify('+2 day');
        }
        $start2 = $dateS->format(TIME_FORMAT);
        $dateS->modify('+1 hour');
        $end2 = $dateS->format(TIME_FORMAT);
        $this->pdo->execQuery('INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent) VALUES ("'.$id_user.'",'
            .' "1", "test", "'.$start2.'", "'.$end2.'", "'.$id_event.'")');

        $result = $this->modelEvents->editEvent([
            'hash' => 'test',
            'id_user' => $id_user,
            'checked' => [
                'event_id' => $id_event,
                'booked_for' => $id_user,
                'dateTimeStart' => $start,
                'dateTimeEnd' => $end,
                'description' => 'test Update recurring'
            ],
            'timestamp' => $start,
        ]);
        $this->assertTrue($result);
        $this->pdo->execQuery('DELETE FROM events WHERE id_user='.$id_user);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testEditEventNoValidate()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();


        $result = $this->modelEvents->editEvent([
            'hash' => 'test',
            'id_user' => $id_user,
        ]);
        $this->assertEquals(ERR_FIELDS, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }
}