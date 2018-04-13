<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		redirect('admin/auth');
	}
	
	public function index()
	{
		redirect('admin/auth');
	}
}
