<?php
class DB {
	
	private static $host   = 'preumbranet.domaincommysql.com';
	private static $name   = 'cold_calendars_test';
	private static $user   = 'backend';
	private static $pass   = 'wearewinners';
	
	public static function getNewConnection() {
	  return new Mysqli(self::$host
	  		          ,self::$user
	  		          ,self::$pass
	  			      ,self::$name);
	}
}

?>