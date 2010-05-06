<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/framework/db.php");

class Site 
{
	public $id = '';
	public $code = '';
	public $title = '';
	public $strapline = '';
	public $meta_title = '';
	public $meta_description = '';
	public $meta_keywords = '';
	public $footer = '';
	public $email = '';
	public $root = '';
	public $last_update = '';	
	public $google_analytics_acc = '';	
	public $google_adsense_menu = '';	
	public $google_adsense_sidepanel = '';	
	public $google_adsense_search = '';	
}

class Sites
{	
	public function update($form)
	{		
		$site = $this->row2Object($form);
		
		$this->data_update($site);
		
		return $site;
	}	

	public function get()
	{			
		$row = $this->data_get();
		if ($row === false)
			return false;
		
		return $this->row2Object($row);
	}	
	
	public function row2Object($row)
	{
		$obj = new Site();
		if (isset($row["id"]))
			$obj->id = $row["id"];
			
		$obj->code = $row["code"];
		$obj->title = $row["title"];
		$obj->strapline = $row["strapline"];
		$obj->meta_title = $row["metatitle"];
		$obj->meta_description = $row["metadescription"];
		$obj->meta_keywords = $row["metakeywords"];
		$obj->footer = $row["footer"];
		$obj->email = $row["email"];
		$obj->root = $row["root"];
		if (isset($row["lastupdate"]))
			$obj->last_update = $row["lastupdate"];
		$obj->google_analytics_acc = $row["google_analytics_acc"];
		$obj->google_adsense_menu = $row["google_adsense_menu"];
		$obj->google_adsense_sidepanel = $row["google_adsense_sidepanel"];
		$obj->google_adsense_search = $row["google_adsense_search"];
			
		return $obj;
	}

	public function data_update()
	{		
		$db = new DB();
		return $db->query(sprintf("update sites set	code = %s , 	title = %s , 	strapline = %s , 	metatitle = %s , 	metadescription = %s , 	metakeywords = %s , 	footer = %s ,	email = %s , 	root = %s , 	lastupdate = now(), google_adsense_menu = %s, google_adsense_search = %s, google_adsense_sidepanel = %s, google_analytics_acc = %s", $db->escapeString($site->code), $db->escapeString($site->title), $db->escapeString($site->strapline), $db->escapeString($site->meta_title), $db->escapeString($site->meta_description), $db->escapeString($site->meta_keywords), $db->escapeString($site->footer), $db->escapeString($site->term_singular), $db->escapeString($site->term_plural), $db->escapeString($site->email), $db->escapeString($site->root), $db->escapeString($site->google_adsense_menu), $db->escapeString($site->google_adsense_search), $db->escapeString($site->google_adsense_sidepanel), $db->escapeString($site->google_analytics_acc)));
	}

	public function data_get()
	{			
		$db = new DB();
		return $db->queryOneRow("select * from site ");		
	}	
}
?>