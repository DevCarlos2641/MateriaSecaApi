<?php
class DB_Connect {

    var $connection = null;

    function __construct(){}

    function __destruct() 
    {
        $this->close();
    }

    public function connect() 
    {
        try {
            $this->connection = new PDO('mysql:host=127.0.0.1;port=3306;dbname=u517350403_materiaseca_ap', 'u517350403_adminmsap', 'Aguacates-2024', [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
            $this->close();
        }
        return $this->connection;
    }

    public function close() 
    {
        unset($this->connection);
    }

}
?>
