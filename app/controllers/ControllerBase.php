<?php

// use Phalcon\Mvc\Controller;

class ControllerBase extends Phalcon\Di\Injectable
{
	public function __construct()
	{
		if(!isset($this->db) || is_null($this->db))
			$this->db->connect();
	}

	public function __destruct()
	{
		$this->db->close();
	}
}
