<?php

require_once("config.php");
require_once(WWW_DIR."/lib/users.php");
require_once(SMARTY_DIR.'Smarty.class.php');

class BasePage 
{
	public $title = '';
	public $content = '';
	public $head = '';
	public $body = '';
	public $meta_keywords = '';
	public $meta_title = '';
	public $meta_description = '';    
	public $page_template = ''; 
	public $smarty = '';
	public $userdata = array();
	public $serverurl = '';
		
	const FLOOD_WAIT_SECONDS = 1.000;
	const FLOOD_PUNISHMENT_SECONDS = 3.0;
	
	function BasePage()
	{			
		@session_start();
	
		if((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || ini_get('magic_quotes_sybase'))
		{
            foreach($_GET as $k => $v) $_GET[$k] = stripslashes($v);
            foreach($_POST as $k => $v) $_POST[$k] = stripslashes($v);
            foreach($_COOKIE as $k => $v) $_COOKIE[$k] = stripslashes($v);
        }	
		
		$this->smarty = new Smarty();
		
		$this->smarty->template_dir = WWW_DIR.'templates/';
		$this->smarty->compile_dir  = SMARTY_DIR.'templates_c/';
		$this->smarty->config_dir   = SMARTY_DIR.'configs/';
		$this->smarty->cache_dir    = SMARTY_DIR.'cache/';				

		$this->smarty->assign('page',$this);
		if (isset($_SERVER["SERVER_NAME"]))
		{
			$this->serverurl = (isset($_SERVER["HTTPS"]) ? "https://" : "http://").$_SERVER["SERVER_NAME"].($_SERVER["SERVER_PORT"] != "80" ? ":".$_SERVER["SERVER_PORT"] : "").WWW_TOP.'/';
			$this->smarty->assign('serverroot', $this->serverurl);
		}
		
		$users = new Users();
		if ($users->isLoggedIn())
		{
			$this->userdata = $users->getById($users->currentUserId());
			$this->smarty->assign('userdata',$this->userdata);	
			$this->smarty->assign('loggedin',"true");
			if (isset($_COOKIE['sabnzbd_'.$users->currentUserId().'__apikey']) && $_COOKIE['sabnzbd_'.$users->currentUserId().'__apikey'] != "")
				$this->smarty->assign('sabintegrated',"true");
			if ($this->userdata["role"] == Users::ROLE_ADMIN)
				$this->smarty->assign('isadmin',"true");	
				
			$this->floodCheck(true, $this->userdata["role"]);
		}
		else
		{
			$this->smarty->assign('isadmin',"false");	
			$this->smarty->assign('loggedin',"false");	

			$this->floodCheck(false, "");
		}
	}    
	
	public function floodCheck($loggedin, $role)
	{
		//
		// if flood wait set, the user must wait x seconds until they can access a page
		//
		if (empty($argc) && 
			$role != Users::ROLE_ADMIN &&
			isset($_SESSION['flood_wait_until']) && 
			$_SESSION['flood_wait_until'] > microtime(true))
			{
				$this->showFloodWarning();
			}
		else
		{
			if(empty($argc) && $role != Users::ROLE_ADMIN)
			{
				if (!isset($_SESSION['flood_check']))
				{
					$_SESSION['flood_check'] = "1_".microtime(true);
				}
				else
				{
					$hit = substr($_SESSION['flood_check'], 0, strpos($_SESSION['flood_check'], "_", 0));
					if ($hit >= 3)
					{
						$onetime = substr($_SESSION['flood_check'], strpos($_SESSION['flood_check'], "_") + 1);
						if ($onetime + BasePage::FLOOD_WAIT_SECONDS > microtime(true))
						{
							$_SESSION['flood_wait_until'] = microtime(true) + BasePage::FLOOD_PUNISHMENT_SECONDS;
							unset($_SESSION['flood_check']);
							$this->showFloodWarning();
						}
						else 
						{
							$_SESSION['flood_check'] = "1_".microtime(true);
						}
					}
					else
					{
						$hit++;
						$_SESSION['flood_check'] = $hit.substr($_SESSION['flood_check'], strpos($_SESSION['flood_check'], "_", 0));
					}
				}
			}
		}
	}
	
	//
	// Done in html here to reduce any smart processing burden if a large flood is underway
	//
	public function showFloodWarning()
	{
		echo "
			<html>
			<head>
				<title>Service Unavailable</title>
			</head>

			<body>
				<h1>Service Unavailable</h1>

				<p>Too many requests!</p> 

				<p>You must <b>wait ".BasePage::FLOOD_PUNISHMENT_SECONDS." seconds</b> before trying again.</p> 

			</body>
		</html>";
		die();
	}
	
	public function addToHead($headcontent) 
	{			
		$this->head = $this->head."\n".$headcontent;
	}	
	
	public function addToBody($attr) 
	{			
		$this->body = $this->body." ".$attr;
	}		
	
	public function render() 
	{
		$this->smarty->display($this->page_template);
	}
	
	public function isPostBack()
	{
		return (strtoupper($_SERVER["REQUEST_METHOD"]) === "POST");	
	}
	
	public function show404()
	{
		header("HTTP/1.1 404 Not Found");
		die();
	}
	
	public function show403($from_admin = false)
	{
		$redirect_path = ($from_admin) ? str_replace('/admin', '', WWW_TOP) : WWW_TOP;
		header("Location: $redirect_path/login");
		die();
	}
}
?>
