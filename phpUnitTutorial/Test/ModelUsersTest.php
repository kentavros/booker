<?php
require_once '../../server/app/lib/config.php';
require_once 'Db.php';
require_once '../../server/app/models/ModelDB.php';
require_once '../../server/app/lib/Validator.php';
require_once '../../server/app/models/ModelUsers.php';
class ModelUsersTest extends PHPUnit_Framework_TestCase
{
    protected $modelUsers;
    protected $pdo;

    protected function setUp()
    {
        $this->modelUsers = new ModelUsers();
        $this->pdo = new Db();
    }
    protected function tearDown()
    {
        $this->modelUsers = NULL;
        $this->pdo = NULL;
    }

    public function testGetUsersAllUsers()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->getUsers([
            'hash' => 'test',
            'id_user' => $id_user,
        ]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) > 0);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testGetUsersById()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->getUsers([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_user
        ]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) > 0);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testGetUsersAccessDenied()
    {
        $result = $this->modelUsers->getUsers([
            'hash' => 'test',
            'id_user' => '1',
        ]);
        $this->assertEquals(ERR_ACCESS, $result);
    }

    public function testAddUserTrue()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "test_booker4884", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->addUser([
            'hash' => 'test',
            'id_user' => $id_user,
            'username' => 'test_booker_new',
            'id_role' => '1',
            'login' => 'testBookerEvents',
            'pass' => 'test4884',
            'email' => 'test4884booker@email.com'
        ]);
        $this->assertEquals(1, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE login="testBookerEvents"');
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testAddUserDublicateLoginError()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "testBookerEvents", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->addUser([
            'hash' => 'test',
            'id_user' => $id_user,
            'username' => 'test_booker_new',
            'id_role' => '1',
            'login' => 'testBookerEvents',
            'pass' => 'test4884',
            'email' => 'test4884booker@email.com'
        ]);
        $this->assertEquals(ERR_LOGIN, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE login="testBookerEvents"');
    }

    public function testAddUserAccessDenied()
    {
        $result = $this->modelUsers->addUser([
            'hash' => 'test',
            'id_user' => '1',
        ]);
        $this->assertEquals(ERR_ACCESS, $result);
    }

    public function testEditUserTrueWithoutPass()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "testBookerEvents", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->editUser([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_user,
            'username' => 'EditName',
            'role' => '2',
            'email' => 'test4884_2@email.com'
        ]);
        $this->assertEquals(1, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testEditUserTrueWithPass()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "testBookerEvents", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->editUser([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_user,
            'username' => 'EditName',
            'role' => '2',
            'email' => 'test4884_2@email.com',
            'pass' => 'test'
        ]);
        $this->assertEquals(1, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testEditUserErrorAccess()
    {
        $result = $this->modelUsers->editUser([
            'hash' => 'test',
            'id_user' => '1',
        ]);
        $this->assertEquals(ERR_ACCESS, $result);
    }

    public function testLoginUserTrue()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash)'
            .' VALUES ("2", "testBookerEvents", "'.md5(md5(trim('1111'))).'", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->loginUser([
            'login' => 'testBookerEvents',
            'pass' => '1111'
        ]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) > 0);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testLoginUserErrorField()
    {
        $result = $this->modelUsers->loginUser([
            'login' => 'testBookerEvents',
            'pass' => ''
        ]);
        $this->assertEquals(ERR_FIELDS, $result);
    }

    public function testLoginUserErrorSearch()
    {
        $result = $this->modelUsers->loginUser([
            'login' => 'testBookerEvents',
            'pass' => '1111'
        ]);
        $this->assertEquals(ERR_SEARCH, $result);
    }

    public function testLoginUserErrorLogin()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash)'
            .' VALUES ("2", "testBookerEvents", "'.md5(md5(trim('1111'))).'", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->loginUser([
            'login' => 'testBookerEvents',
            'pass' => '1211'
        ]);
        $this->assertEquals(ERR_AUTH, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testDeleteUserTrueUserDel()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "testBookerEvents", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("1", "testBookerEvents", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user2 = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->deleteUser([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_user2
        ]);
        $this->assertEquals(1, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }

    public function testDeleteUserTrueAdminDelNotAlone()
    {
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "testBookerEvents", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user = $this->pdo->getPdo()->lastInsertId();
        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "testBookerEvents", "test4884", "test4884", "test4884@email.com", "test")');
        $id_user2 = $this->pdo->getPdo()->lastInsertId();
        $result = $this->modelUsers->deleteUser([
            'hash' => 'test',
            'id_user' => $id_user,
            'id' => $id_user2
        ]);
        $this->assertEquals(1, $result);
        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
    }
//On empty Table only - uncommit
//    public function testDeleteUserTrueAdminDelAlone()
//    {
//        $this->pdo->execQuery('INSERT INTO users (id_role, login, pass, username, email, hash) VALUES ("2", "testBookerEvents", "test4884", "test4884", "test4884@email.com", "test")');
//        $id_user = $this->pdo->getPdo()->lastInsertId();
//        $result = $this->modelUsers->deleteUser([
//            'hash' => 'test',
//            'id_user' => $id_user,
//            'id' => $id_user
//        ]);
//        $this->assertEquals(ERR_A_DEL, $result);
//        $this->pdo->execQuery('DELETE FROM users WHERE id='.$id_user);
//    }
}