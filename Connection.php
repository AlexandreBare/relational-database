<?php
// This class is inspired from https://www.php.net/manual/en/language.oop5.magic.php
// It is used as a wrapper to enable the serialization as PDO object can't be serialized
class Connection
{
    protected $link; // PDO object
    private $dsn, $username, $password; // see PDO object properties

    // Constructor
    public function __construct($dsn, $username, $password)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->connect(); // Connection to the database
    }

    // Connection to the database via a PDO object
    private function connect()
    {
        $this->link = new PDO($this->dsn, $this->username, $this->password);
        if($this->link == NULL)
        	echo "Probleme de connection";
        $this->link->exec("SET CHARACTER SET utf8"); // UTF8 support
    }

    // -- Link to PDO useful functions --
    public function query($query){
        return $this->link->query($query);
    }

    public function prepare($query){
      return $this->link->prepare($query);
    }

    public function execute($array){
      return $this->link->execute($array);
    }

    public function exec($query){
      return $this->link->exec($query);
    }

    public function beginTransaction(){
      return $this->link->beginTransaction();
    }

    public function commit(){
      return $this->link->commit();
    }

    public function rollBack(){
      return $this->link->rollBack();
    }
    // --

    // -- Functions used for serialization --
    public function __sleep()
    {
        return array('dsn', 'username', 'password');
    }

    public function __wakeup()
    {
        $this->connect();
    }
    // --

}
?>
