<?php
	class DatabaseContent //Defining the class
	{
    private static $dbName = 'benrud_SnapShotPosts' ; //This is the name of the database
    private static $dbHost = 'localhost' ; //The host is localhost because it's hosted on the same computer
    private static $dbUsername = 'benrud_cosmeC'; //This is the username for the database access
    private static $dbUserPassword = 'SnapshotBackEndPost42069'; //This is the password
    
    private static $cont  = null; //defining content
     
    public function __construct() { //Constructing the connection
        die('Init function is not allowed'); //In case it does it tells you why
    }
     
    public static function connect()// function to connect to database
    {
       // One connection through whole application
       if ( null == self::$cont ) 
       {
        try
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword); //This functions does the request to self
        }
        catch(PDOException $e)
        {
          die($e->getMessage()); //In case it doesn't work returns the error
        }
       }
       return self::$cont; 
    }
     
    public static function disconnect() //Function to disconnect from database
    {
        self::$cont = null;
    }
    }
?>