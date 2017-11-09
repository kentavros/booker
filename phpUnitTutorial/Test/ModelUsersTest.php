<?php
require_once '../../server/app/lib/config.php';
require_once 'Db.php';
require_once '../../server/app/models/ModelDB.php';
require_once '../../server/app/lib/Validator.php';
require_once '../../server/app/models/ModelUsers.php';
class ModelUsers extends PHPUnit_Framework_TestCase
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
}