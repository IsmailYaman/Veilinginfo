<?php

class Template extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		
		$this->lang->load('admin/common', $this->site_language);
	}

	public function load_default_template($data = NULL)
	{
		//data
		$data['menu_items']	 = $this->build_front_menu();
		$data['footer_menu'] = $this->build_footer_menu();
		$data['description'] = isset($data['description']) ? $data['description'] : '';
		
		$page_title 		= isset($data['page_title']) ? ' - '.$data['page_title'] : '';
		$data['site_title']	= $this->M_config->get('site_title').$page_title;
		$data['site_name']	= $this->M_config->get('site_title');
		
		//render to screen
		$this->load->view('template/default_template_v', $data);
	}
	
	public function load_admin_template($data = NULL)
	{
		//Language
		$data['msg_confirm'] 			 = $this->lang->line('msg_confirm');
		$data['date_last_access_format'] = $this->lang->line('date_last_access_format');
		$data['string_last_access'] 	 = $this->lang->line('string_last_access');
		$data['string_logout'] 			 = $this->lang->line('string_logout');

		//data vars
		$data['site_name']			= $this->M_config->get('site_title');
		$data['site_title']			= $data['site_title'] ? $data['site_title'] : $this->M_config->get('site_title');
		$data['member_data']		= $this->member_data;
		$data['menu_items']			= $this->build_admin_menu();
		$data['is_superadmin']		= $this->_is_superadmin();
		$data['can_add_page']		= $this->_can_add_page();
		
		//Hrefs
		$data['href_dashboard']		= base_url()."admin/dashboard" . '?token=' . $this->token;
		$data['href_account']		= base_url()."admin/account" . '?token=' . $this->token;
		$data['href_links']			= base_url()."admin/links" . '?token=' . $this->token;
		$data['href_categories']	= base_url()."admin/categories" . '?token=' . $this->token;

		//render to screen
		$this->load->view('template/admin_template_v', $data);
	}
	
	public function load_admin_login_template($data = NULL)
	{
		//Language
		$data['string_admin_text'] = $this->lang->line('string_admin_text');
		$data['string_login_text'] = $this->lang->line('string_login_text');
		$data['copyright_text']    = sprintf($this->lang->line('copyright_text'), 'veilinginfo.nl');
		//$data['copyright_text']    = sprintf($this->lang->line('copyright_text'), '<a href="http://www.adtrader.nl" target="_blank">Adtrader.nl</a>');
		
		//data
		$data['site_title'] = $this->M_config->get('site_title');
		
		//render to screen
		$this->load->view('template/admin_login_template_v', $data);
	}
	
	public function build_front_menu()
	{
		
		$this->lang->load('front/menu', $this->site_language);
		$this->load->model('M_info');
		
		//front menu items
		$items[] = array("label" => $this->lang->line('menu_home'), 		"href" => "", 					 "position" => "left");
		//$items[] = array("label" => $this->lang->line('menu_link_request'), "href" => url_alias('linkform'), "position" => "left");

		$menu = $this->M_info->get_all_infos(false, 1);

		foreach($menu as $item)
		{
			$items[] = array("label" => $item->menu_title, "href" => $item->slug, "position" => "left");
		}
		
		if(!Is_Subdomain())
		{
			//$items[] = array("label" => $this->lang->line('menu_page_request'), "href" => url_alias('pageform'), "position" => "left");
			//$items[] = array("label" => $this->lang->line('menu_pages'), 		"href" => url_alias('pages'), 	 "position" => "right");
		}
		
		$items[] = array("label" => $this->lang->line('menu_contact'), "href" => url_alias('contactform'), "position" => "right");
		
		$menu['left']  = '';
		$menu['right'] = '';
		
		foreach($items as $t)
		{
			$menu[$t['position']] .= '<li class="' . (($this->_get_uri() == $t['href']) ? 'nav-item active' : 'nav-item') . '"><a class="nav-link" href="'.format_page_url($this->current_page).'/'.$t['href'].'"><span>'.$t['label'].'</span></a></li>';
		}
		
		return $menu;
	}
	
	public function build_admin_menu()
	{
		$this->load->model('admin/M_admin_dashboard');
		
		$total_new_messages = $this->M_admin_dashboard->get_messages_total(0, $this->_member_pages(false, true), $this->_is_superadmin());
		
		//admin menu items
		$items[] = array("label" => $this->lang->line('menu_dashboard'),	"href" => "dashboard",  "icon" => "dashboard");
		$items[] = array("label" => $this->lang->line('menu_pages'),		"href" => "pages", 		"icon" => "folder-open");
		$items[] = array("label" => $this->lang->line('menu_categories'),	"href" => "categories", "icon" => "bars");
		$items[] = array("label" => $this->lang->line('menu_info'),			"href" => "info", 	 	"icon" => "info-circle");
		$items[] = array("label" => $this->lang->line('menu_links'),		"href" => "links", 	 	"icon" => "link");
		$items[] = array("label" => $this->lang->line('menu_spotlight'),	"href" => "spotlight", 	 "icon" => "gavel");
		$items[] = array("label" => $this->lang->line('menu_members'), 		"href" => array(0 => array("label" => $this->lang->line('menu_list'), "href" => "member/list"), 1 => array("label" => $this->lang->line('menu_groups'), "href" => "member/group")), "icon" => "users");
		$items[] = array("label" => $this->lang->line('menu_modules'),		"href" => "module", 	 "icon" => "expand");
		$items[] = array("label" => $this->lang->line('menu_messages'). ' (' . $total_new_messages . ')',		"href" => array(0 => array("label" => $this->lang->line('menu_message_new'), "href" => "messages/open"), 1 => array("label" => $this->lang->line('menu_message_archive'), "href" => "messages/archive")), 	 	"icon" => "envelope");
		$items[] = array("label" => $this->lang->line('menu_languages'),	"href" => "languages", 	 "icon" => "language");
		$items[] = array("label" => $this->lang->line('menu_settings'),		"href" => "settings", 	 "icon" => "cog");
		
		$menu = '';
		foreach($items as $t)
		{
			if(is_array($t['href']))
			{ 
				$active='';
				$t_menu= '';
				
				$active=false;
				$item_c = 0;
				foreach($t['href'] as $drop)
				{
					if($this->_has_permission('read', $drop['href']))
					{
						$class='';
						if($this->_get_uri() == $drop['href']){ $active=true; $class="active-menu"; }
						$t_menu .= '<li><a class="' . $class . '" href="' . base_url() . 'admin/' . $drop['href'] . '?token=' . $this->token . '">' . $drop['label'] . '</a></li>';
						
						$item_c++;
					}
				}
				
				if($item_c > 0)
				{
					$menu .= '<li class="' . ($active ? 'active' : '') . '"><a href="#"><i class="fa fa-' . $t['icon'] . '"></i> ' . $t['label'] . '<span class="fa arrow"></span></a><ul aria-expanded="false" class="nav nav-second-level">';
					$menu .= $t_menu;
					$menu .= '</ul></li>';	
				}

			} else {
				
				if($this->_has_permission('read', $t['href']))
				{
					$menu .= '<li><a class="' . ($this->_get_uri() == $t['href'] ? 'active-menu' : '') . '"  href="' . base_url() . 'admin/' . $t['href'] . '?token=' . $this->token . '"><i class="fa fa-' . $t['icon'] . '"></i> ' . $t['label'] . '</a></li>';             
				}
				
			}

		}
		return $menu;
	}
	
	public function build_footer_menu()
	{
		$this->load->model('M_info');
		
		$menu = $this->M_info->get_all_infos(false, 2);
		$footer = array();
		foreach($menu as $item)
		{
			$footer[] = '<a href="' . base_url() . $item->slug . '" title="' . $item->menu_title . '">' . $item->menu_title . '</a>'; 
		}
		
		return implode(' | ', $footer);
	}

}