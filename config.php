<?php 

class Config
{
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "student";
    private $tableName = "students";
    private $connection;

    public function connect()
    {
        $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->database);
    }

    public function __constructor()
    {
        $this->connect();
    }

    public function insertData($name, $age, $course, $contact)
    {
        $query = "INSERT INTO students (name, age, contact, course) VALUES ('$name', '$age', '$contact', '$course')";
        mysqli_query($this->connection, $query);
    }
}

?>