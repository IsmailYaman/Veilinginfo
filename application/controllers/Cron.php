<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('admin/common', $this->site_language);
	}

	public function index()
	{
		die('no access');
	}
	
	public function messages()
	{
		$this->load->model('M_cron');
		
		//get link requests
		$messages = $this->M_cron->get_new_messages();
		$store = array();
		foreach($messages as $message)
		{
			if(!isset($store[$message->member_id]))
			{
				//check if there are any messages already notified
				$old_messages 								= $this->M_cron->get_new_messages_member(2,1, $message->member_id);
				$store[$message->member_id]['member_id'] 	= $message->member_id;
				$store[$message->member_id]['count'] 		= $old_messages;
			}
			$store[$message->member_id]['count']++;
			
			$update = $this->db->update('messages', array("notified" => 1), array("message_id" => (int)$message->message_id) );
		}
		
		//get page requests
		$messages_admin = $this->M_cron->get_new_messages(1);
		foreach($messages_admin as $message_admin)
		{
			if(!isset($store[0]))
			{
				//check if there are any messages already notified
				$old_messages			= $this->M_cron->get_new_messages_member(1, 1, 0);
				$store[0]['member_id'] 	= 0;
				$store[0]['count'] 		= $old_messages;
			}
			$store[0]['count']++;
			
			$update = $this->db->update('messages', array("notified" => 1), array("message_id" => (int)$message_admin->message_id) );
		}

		//run the whole array for mailing
		foreach($store as $mail)
		{
			if($mail['member_id'] > 0)
			{
				$this->load->model('admin/M_admin_members');
				$member = $this->M_admin_members->get_member($mail['member_id']);
			}
			else
			{
				$member['firstname'] 	= $this->M_config->get('email_from_name');
				$member['lastname'] 	= '';
				$member['email'] 		= $this->M_config->get('email_from_address'); 
			}
			
			$params = array(
				"firstname" 	=> $member['firstname'],
				"lastname" 		=> $member['lastname'],
				"login" 		=> base_url().'admin/auth/',
				"count" 		=> $mail['count'],
				"site_name" 	=> $this->M_config->get('site_title')
			);

			
			$subject = sprintf($this->lang->line('mail_subject_new_messages'), $this->M_config->get('site_title'));
			
			$this->_send_email($member['email'], $subject, "new_messages_mail", $params);
			
		}

	}

}
	
?>