<?php
defined( '_NE' ) or die( 'Restricted access' );

class DB {
	const DB_TEMPLATE=1;
	const DB_CONTENT=2;
	const DB_CHUNK=3;
	
	private $dbobj;
	private $DB_cache;
	private $DB_dbh;
	private $DB_template;
	private $DB_content;
	//db configuration 
	private $hostname = "127.0.0.1";
	private $username = "max";
	private $password = "maxmax";
	private $dbName = "max";
	private $userstable = "modx_site_content";
	private $templatetable = "modx_site_templates";
	private $chunktable = "modx_site_htmlsnippets";

	public function __construct(){
		} 
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
		$this->DB_cache = new XCache ();
				
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
				
		$conn=$this->BaseConnect();
		$content=$this->GetContent($uri, 1);
		$template=$this->GetContent($content["template"], 0);

		// -------------------------------------Make the output and ---Get chunks----------------------------------------
		$out = preg_replace( "/\[\[\*content\]\]/", $content["content"], $template["content"]);
		unset ($content);
		preg_match_all("/\[\[[$]{0,}([a-zA-Z0-9_]{1,})([[?]{0,1}[a-zA-Z0-9_&=`\s]{0,}]{0,})\]\]/", $template["content"], $chunkarray, PREG_SET_ORDER);
		// $chunkarray[0]<-ChunkString $chunkarray[1]<- ChunkName $chunkarray[3]<- Parametrs
		
		foreach ($chunkarray as $chunk)
		{
			$content=$this->GetContent($chunk[1], 2);
			$out = str_replace($chunk[0], $content["content"], $out);
		};
		
		$out = str_replace ("[[++site_url]]","http://localhost/",$out);
		
		return $out;

	}

	private function GetContent ($key, $type){
		switch ($type) {
			case 0: //Get template
				//$this->DB_dbh
				$cache = $this->DB_cache->get($key);
				if (!isset($cache))
				{
				$sth = $this->DB_dbh->prepare("SELECT content as 'content' FROM ".$this->templatetable." WHERE id= '".$key."' LIMIT 0 , 1");
				$sth->execute();
				$arr=$sth->fetch(PDO::FETCH_ASSOC);
				$sth->closeCursor();
				$this->DB_cache->set($key,serialize($arr));
				}
				else {$arr=unserialize($cache);}
				break;
			case 1: //Get content
				$cache = $this->DB_cache->get($key);
				if (!isset($cache))
				{
				$sth = $this->DB_dbh->prepare("SELECT id as 'id', pagetitle as 'title' , template as 'template', content as 'content' FROM ".$this->userstable." WHERE uri= '".$key."' LIMIT 0 , 1");
				$sth->execute();
				$arr = $sth->fetch(PDO::FETCH_ASSOC);
				$sth->closeCursor();
				$this->DB_cache->set($key,serialize($arr));
				}
				else {$arr=unserialize($cache);}
				break;
			case 2: //Get chunks
				$cache = $this->DB_cache->get($key);
				if (!isset($cache))
				{
				$sth = $this->DB_dbh->prepare("SELECT snippet as 'content' FROM ".$this->chunktable." WHERE name='".$key."' LIMIT 1");
				$sth->execute();
				$arr= $sth->fetch(PDO::FETCH_ASSOC);
				$sth->closeCursor();
				$this->DB_cache->set($key,serialize($arr));
				}
				else {$arr=unserialize($cache);}
				break;
		}
		return $arr;
	}
	
}
?>