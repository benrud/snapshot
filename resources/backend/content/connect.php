<?php
	class DatabaseContent
	{
    private static $dbName = 'benrud_SnapShotPosts' ;
    private static $dbHost = 'localhost' ;
    private static $dbUsername = 'benrud_cosmeC';
    private static $dbUserPassword = 'SnapshotBackEndPost42069';
    
    private static $cont  = null;
     
    public function __construct() {
        die('Init function is not allowed');
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