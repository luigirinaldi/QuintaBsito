<?php
class Database{ 
    private $username = "user1";
    private $password = "User1@pssw";
    private $db_name = "school_recordings";
    private $host = "localhost";
    public $conn;
	// database connection
	public function getConnection(){
		$this->conn = null;
		try
			{
			$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->conn->exec("set names utf8");
			}
		catch(PDOException $exception)
			{
			echo "Connection Error: " . $exception->getMessage();
			}
		return $this->conn;
	}
}