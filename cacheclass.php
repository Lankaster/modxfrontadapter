<?php
defined( '_NE' ) or die( 'Restricted access' );

class XCache {
	public function set ($name, $value){
		xcache_set('test'.md5($name), $value);
	}
	public function get ($name){
		return xcache_get('test'.md5($name));
	}
	
}