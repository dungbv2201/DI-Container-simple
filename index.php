<?php
require_once "Container.php";

interface DatabaseInterface{

	public function connect();

}
class Mysql implements DatabaseInterface {

	public function connect()
	{
		return "connect ".__CLASS__." successfully!";
	}
}

class SqlSever implements DatabaseInterface{

	public function connect()
	{
		return "connect ".__CLASS__." successfully!";
	}
}

class User{

	public $database;

	public function __construct(DatabaseInterface $database)
	{
		$this->database = $database;
	}

	public function store(){
		echo $this->database->connect();
	}
}

$container = new Container();
//$container->bind(DatabaseInterface::class, Mysql::class);
$container->bind(DatabaseInterface::class, SqlSever::class);


$user = $container->make(User::class);
echo $user->store();
