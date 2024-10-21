<?php

class Database
{
	private $pdo;
	
	public function __construct()
	{
		$config = require_once '../config/config.php';
		
		try {
			$this->pdo = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_pass']);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			die('Conexão falha: ' . $e->getMessage());
		}
	}
	
	public function query($sql)
	{
		return $this->pdo->query($sql);
	}
}