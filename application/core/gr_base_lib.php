<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GR_Base_Lib {

	public function __construct() 
	{
		$this->ci_instance =& get_instance();
	}

}