<?php
defined( '_NE' ) or die( 'Restricted access' );

class DB {
	private $dbobj;
	private $DB_dbh;
	private $DB_template;
	private $DB_content;
	//db configuration 
	private $hostname = "127.0.0.1";
	private $username = "max";
	private $password = "maxmax";
	private $dbName = "max";
	private $userstable = "modx_site_content";
	private $templatetable = "modx_site_templates_fast";

	public final function __clone()
	{
		throw new BadMethodCallException("Clone is not allowed");
	}
	/**
	 * BaseConnect()
	 * Set or get connection to base
	 * @static
	 * @access private
	 * @return $DB_dbh
	 */
	private function BaseConnect() {
		$this->DB_dbh = new PDO('mysql:host='.$this->hostname.';dbname='.$this->dbName ,$this->username,$this->password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		return $this->DB_dbh;
	}
	/**
	 * BaseClose()
	 * Close connection
	 * @static
	 * @access private
	 * @return null
	 */
	private function BaseClose() {
		//Close connection
		$this->DB_dbh = NULL;
	}
	
	/**
	 * getPageFromAlias
	 *
	 * @param $uri
	 * @static
	 * @access public
	 * @return Get content from uri
	 */
	public function getPageFromAlias($uri) 
	{
		
		//$conn=self::BaseConnect();
		$conn=$this->BaseConnect();
		
		// Get content
		$sth = $conn->prepare("SELECT id as 'id', pagetitle as 'title' , template as 'template', content as 'content' FROM ".$this->userstable." WHERE uri= '".$uri."' LIMIT 0 , 1");
		$sth->execute();
		$content = $sth->fetch(PDO::FETCH_ASSOC);
		$sth->closeCursor();
		
		$sth = $conn->prepare("SELECT content as 'content' FROM ".$this->templatetable." WHERE id= '6' LIMIT 0 , 1");
		$sth->execute();
		$template= $sth->fetch(PDO::FETCH_ASSOC);
		$sth->closeCursor();

		// ----------------------------------------Get chunks----------------------------------------
		//$tmp=preg_match_all("/\[\[\$(SiteHead|MainMenu|ADBigBan|ADBan|ADBanRight|Social|ADBigBan2|SiteFoot)\]\]/",$contents,$out)
		//$template["content"] = preg_replace("/\[\[\$SiteHead\]\]/", , $template["content"]);
		
		return preg_replace( "/\[\[\*content\]\]/", $content["content"], $template["content"]);

	}

}
?>