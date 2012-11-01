<?php
defined( '_NE' ) or die( 'Restricted access' );

class DB {
	private $dbobj;
	private $DB_dbh;
	private $DB_template;
	private $DB_content;
	//db configuration 
	private $hostname = "localhost";
	private $username = "max";
	private $password = "maxmax";
	private $dbName = "max";
	private $userstable = "modx_site_content";
	private $templatetable = "modx_site_templates_fast";

	
	private function __construct()
	{
	}
	public final function __clone()
	{
		throw new BadMethodCallException("Clone is not allowed");
	}
	/**
	 * getInstance
	 *
	 * @static
	 * @access public
	 * @return object DB instance
	 */
	public static function getInstance()
	{
		if (!(self::$dbobj instanceof DB)) {
			self::$dbobj = new DB;
		}
		return self::$dbobj;
	}
	/**
	 * __BaseConnect()
	 * Set or get connection to base
	 * @static
	 * @access private
	 * @return $DB_dbh
	 */
	private function __BaseConnect() {
		if (!(self::$DB_dbh instanceof PDO)) {
		self::$DB_dbh = new PDO("mysql:host='".$hostname."';dbname='".$dbName."'",$username,$password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		}
		return self::$DB_dbh;
	}
	/**
	 * __BaseClose()
	 * Close connection
	 * @static
	 * @access private
	 * @return null
	 */
	private function __BaseClose() {
		//Close connection
		self::$DB_dbh = NULL;
	}
	
	/**
	 * getPageFromAlias
	 *
	 * @param $uri
	 * @static
	 * @access public
	 * @return Get content from uri
	 */
	public static function getPageFromAlias($uri) 
	{
		
		$conn=self::__BaseConnect();
		// Get content
		$sth = $conn->prepare("SELECT id as 'id', pagetitle as 'title' , template as 'template', content as 'content' FROM ".$userstable." WHERE uri= '".$uri."' LIMIT 0 , 1");
		$sth->execute();
		$content = $sth->fetch(PDO::FETCH_ASSOC);
		$sth->closeCursor();
		
		$sth = $conn->prepare("SELECT content as 'content' FROM ".$templatetable." WHERE id= '6' LIMIT 0 , 1");
		$sth->execute();
		$template= $sth->fetch(PDO::FETCH_ASSOC);
		$sth->closeCursor();

		// ----------------------------------------Get chunks----------------------------------------
		//$tmp=preg_match_all("/\[\[\$(SiteHead|MainMenu|ADBigBan|ADBan|ADBanRight|Social|ADBigBan2|SiteFoot)\]\]/",$contents,$out)
		//$template["content"] = preg_replace("/\[\[\$SiteHead\]\]/", , $template["content"]);
		
		
		//printf (preg_replace($chunks['name'], $chunks['snippet'], $template["content"])) ;
		return preg_replace( "/\[\[\*content\]\]/", $content["content"], $template["content"]);

	}

}
?>