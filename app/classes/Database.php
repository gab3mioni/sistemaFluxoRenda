<?php

/**
 *
 * Gerencia a conexão com o banco de dados
 */

class Database
{
    /**
     * @var PDO Instância da conexão PDO com o banco de dados.
     */
	private $pdo;

    /**
     * Construtor da classe
     *
     * Inicializa a conexão com o banco de dados utilizando as configurações
     * definidas em config.php.
     *
     * @throws PDOException se a conexão com o banco de dados falhar.
     */
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
}