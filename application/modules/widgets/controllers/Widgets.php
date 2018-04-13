<?php

class Widgets extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_all_widgets($data = NULL)
	{
		//Get all active Widgets (modules)
		$this->load->model('Widgets/M_widgets');
		$widgets = $this->M_widgets->get_active_modules();

		$widget_columns = array();
		$sort_list		= array();
		foreach($widgets as $widget){
			$this->load->module($widget->machine_name);
			$data['widget_name'] = $widget->name;
			$data['widget_content'] = $this->{$widget->machine_name}->display_data();
			
			$sort_order = $widget->sort_order;
			
			if(isset($sort_list[$widget->column_row][$sort_order]))
			{
				$sort_order = $sort_order+1;
			}
			
			$widget_columns['column_'.$widget->column_row][$sort_order] = $this->load->view('widgets/widgets_v', $data, true);	
			$sort_list[$widget->column_row][$sort_order] = true;
		}

		return $widget_columns;
	}
}