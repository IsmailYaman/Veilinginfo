<?php 

class MY_Controller extends MX_Controller
{
	public $site_language;
	
	public function __construct()
	{
		parent::__construct();
		$this->member_data   = $this->session->userdata('member');
		$this->site_language = $this->M_config->current_language();
		$this->token 	 	 = $this->session->userdata('token');

		$this->lang->load('admin/common', $this->site_language);
		
		$page = CurrentPage();
		$this->current_page = $page['subdomain'];
		
		if($this->uri->segment(1) == "admin" && Is_Subdomain())
		{
			redirect('admin/auth');
		}
		
		$exclude = array("auth", "dashboard", "ajax", "account");
		
		if($this->uri->segment(1) == "admin" && !$this->_logged_in() && !in_array($this->uri->segment(2), $exclude))
		{
			redirect('admin/auth');
		}
		
		if(!in_array($this->uri->segment(2), $exclude))
		{
			if(!$this->_has_permission('read'))
			{
				show_error($this->lang->line('error_read_permission'));
			}
		}
		
		$this->load->module('template');
	}
	
	public function _logged_in()
	{
		return (bool)$this->member_data;
	}
	
	public function _check_login($method='')
	{		
		if(!$this->_logged_in() && $method != 'login')
		{
			redirect('admin/auth');
		}

		if(($this->_logged_in() && $method == 'login' && $this->input->get('token')))
		{
			$this->_go_url('admin/dashboard');
		}

		if($method != 'login')
		{
			$this->_check_token();
		}
		
	}
	
	public function _set_token()
	{
		$this->load->helper('string');
		$this->token = strtolower(random_string('sha1'));
		$this->session->set_userdata('token', $this->token);
	}
	
	public function _check_token()
	{
		$token = $this->input->get('token');
		if((empty($token) || $token != $this->token) && $this->_logged_in())
		{
			redirect('admin/auth');
		}
	}
	
	public function _set_permissions($permissions)
	{
		$group = unserialize($permissions);
		$this->session->set_userdata('permissions', $group);
	}
	
	public function _has_permission($permission, $item = false)
	{
		if($this->uri->segment(1) == "admin" && !empty($this->uri->segment(2)))
		{
			$continue=true;
			if(!$item){ $item = $this->_get_uri(); if(is_numeric($this->uri->segment(3))){ $continue=false; } }
			
			$permissions = $this->session->userdata('permissions');
			if($continue)
			{
				if(!isset($permissions[$permission]) || !in_array($item, $permissions[$permission]))
				{
					return false;
				}	
			}	
		}
		return true;
	}
	
	public function _is_superadmin()
	{
		if(in_array($this->member_data['group'], $this->config->item('super_admin')))
		{
			return true;
		}
		return false;
	}
	
	public function _is_moderator()
	{
		$this->load->model('admin/M_admin_members');
		
		$moderator = $this->M_admin_members->is_member_moderator($this->member_data['member_id']);
		
		if($moderator)
		{
			return true;
		}
		return false;
	}
	
	public function _can_add_page()
	{
		if(in_array($this->member_data['group'], $this->config->item('new_page_groups')))
		{
			return true;
		}
		return false;
	}
	
	public function _destroy_session()
	{
		$this->session->unset_userdata(array('member', 'token', 'permissions'));
	}
	
	public function _go_url($url, $query_string='')
	{
		redirect($url. '?token=' . $this->token . $query_string);
	}
	
	public function _get_uri()
	{
		if($this->uri->segment(1) == "admin")
		{
			$r = array("edit", "add", "delete", "approve"); 
			return $this->uri->segment(3) ? (is_numeric($this->uri->segment(3)) ? $this->uri->segment(2) :  (in_array($this->uri->segment(3), $r) ? $this->uri->segment(2) :  $this->uri->segment(2).'/'.$this->uri->segment(3))) : $this->uri->segment(2);
		}
		else
		{
			return $this->uri->segment(1);
		}
	}
	
	public function _send_email($to, $subject, $template, $params)
	{
		$this->load->library('email');
		$this->email->from($this->M_config->get('email_from_address'), $this->M_config->get('email_from_name'));
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($this->load->view('mail/'.$template, $params, true));
		$this->email->send();
	}
	
	public function _reset_member($member_id, $reason = 'new_account')
	{
		$this->load->helper('string');

		$token 		= strtolower(random_string('alnum', 25));
		$token_sha 	= hash('SHA512', $token);
		$ip 		= $_SERVER['REMOTE_ADDR'];
		
		$this->load->model('admin/M_admin_members');
		$member = $this->M_admin_members->get_member($member_id);
		
		$this->M_admin_members->insert_reset_token($member_id, $member['email'], $ip, $token_sha);
		
		$mail = array(
			"new_account" 	=> array("subject" => sprintf($this->lang->line('mail_subject_new_account'), $this->M_config->get('site_title')), "template" => "new_member_mail"),
			"reset_account" => array("subject" => sprintf($this->lang->line('mail_subject_reset_account'), $this->M_config->get('site_title')), "template" => "reset_member_mail")
		);
		
		$params = array(
			"firstname" 	=> $member['firstname'],
			"lastname" 		=> $member['lastname'],
			"reset_link" 	=> base_url().'admin/auth/reset_confirm/'.urlencode($token_sha),
			"site_name" 	=> $this->M_config->get('site_title')
		);
		
		$this->_send_email($member['email'], $mail[$reason]["subject"], $mail[$reason]["template"], $params);
	}
	
	public function _member_pages($array = false, $return_pages = false)
	{
		$this->load->model('admin/M_admin_members');
				
		$pages  = $this->M_admin_members->get_member_pages($this->member_data['member_id']);
		$return = 0;

		if($this->_is_moderator() || $this->_is_superadmin())
		{
			$admin_pages = $this->M_admin_members->get_member_pages(0);
			$pages = array_merge($pages, $admin_pages);
		}
		
		if($pages)
		{
			$return = implode(',', $pages);

			if($array)
			{
				$return = $pages;
			}
		}

		if($this->_is_superadmin() && !$return_pages)
		{
			$return = "all";
		}
		
		return $return;
	}
	
	public function _member_categories($array = false)
	{
		$this->load->model('admin/M_admin_members');
				
		$pages  = $this->M_admin_members->get_member_categories($this->member_data['member_id']);
		$return = implode(',', $pages);
		
		if($array)
		{
			$return = $pages;
		}
		
		return $return;
	}
	
}