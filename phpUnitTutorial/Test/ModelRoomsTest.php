<?php
require_once '../../server/app/lib/config.php';
require_once 'Db.php';
require_once '../../server/app/models/ModelDB.php';
require_once '../../server/app/lib/Validator.php';
require_once '../../server/app/models/ModelRooms.php';
class ModelRoomsTest extends PHPUnit_Framework_TestCase
{
    protected $modelRooms;
    protected $pdo;

    protected function setUp()
    {
        $this->modelRooms = new ModelRooms();
        $this->pdo = new Db();
    }
    protected function tearDown()
    {
        $this->modelRooms = NULL;
        $this->pdo = NULL;
    }

    public function testGetRoomsAllRooms()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO rooms (name) VALUES ("Test Boardroom")');
        $id_room = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelRooms->getRooms([
            'hash' => 'test',
            'id_user' => $id_user,
        ]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) > 0);
        $this->pdo->execQuery('DELETE FROM rooms WHERE id='.$id_room);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }


    public function testGetRoomsByIdRoom()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO rooms (name) VALUES ("Test Boardroom")');
        $id_room = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelRooms->getRooms([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_room
        ]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) > 0);
        $this->pdo->execQuery('DELETE FROM rooms WHERE id='.$id_room);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testGetRoomsAccessDenied()
    {
        $result = $this->modelRooms->getRooms([
            'hash' => 'test',
            'id_user' => '1',
        ]);
        $this->assertEquals(ERR_ACCESS, $result);
    }
}