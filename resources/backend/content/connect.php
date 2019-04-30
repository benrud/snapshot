<!-- This is the class for the databse -->
<!-- This file never changes unless you need to update the dabatase -->

<?php
	class DatabaseContent //Defining the class
	{
    private static $dbName = 'benrud_SnapShotPosts' ; //This is the name of the database
    private static $dbHost = 'localhost' ; //The host is localhost because it's hosted on the same computer
    private static $dbUsername = 'benrud_cosmeC'; //This is the username for the database access
    private static $dbUserPassword = 'SnapshotBackEndPost42069'; //This is the password
    
    private static $cont  = null; //defining content
     
    public function __construct() { //Constructing the file
        die('Init function is not allowed'); //Constructing other things
    }
     
    public static function connect()
    {
       // One connection through whole application
       if ( null == self::$cont )
       {
        try
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword);
        }
        catch(PDOException $e)
        {
          die($e->getMessage());
        }
       }
       return self::$cont;
    }
     
    public static function disconnect()
    {
        self::$cont = null;
    }
    }
?>