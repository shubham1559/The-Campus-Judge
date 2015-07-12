<?php
/**
 * Sharif Judge online judge
 * @file Notifications.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Comments extends CI_Controller
{

	private $query_edit;
	//public $notice="admin";

	// ------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')) // if not logged in
			redirect('login');
		$this->load->model('query_model');
		$this->query_edit = FALSE;
	}


	// ------------------------------------------------------------------------


	public function index($assignment_id = NULL)
	{
		if ( $this->user->level <=0) // permission denied
			show_404();
		if ($assignment_id === NULL)
			$assignment_id = $this->user->selected_assignment['id'];
		if ($assignment_id == 0)
			show_error('No assignment selected.');
		$data = array(
			'all_assignments' => $this->assignment_model->all_assignments(),
			'queries' => $this->query_model->get_all_queries($assignment_id)
		);
		$this->twig->display('pages/admin/chat.twig', $data);

	}


	// ------------------------------------------------------------------------


	public function add()
	{
		if ( $this->user->level <=0) // permission denied
			show_404();

		$this->form_validation->set_rules('title', 'title', 'trim|required');
		$this->form_validation->set_rules('text', 'text', 'required'); /* todo: xss clean */
		$this->form_validation->set_rules('problem','Problem Id','greater_than[0]');
		if($this->form_validation->run()){
				$values=[];
				$values['title']=$this->input->post('title');
				$values['message']=$this->input->post('text');
				$values['assignment']=$this->user->selected_assignment['id'];
				$values['problem']=$this->input->post('problem');
				$values['source']=$this->user_model->username_to_user_id($this->user->username);
				$values['private']=0;
				$values['time']=shj_now_str();
				if($this->input->post('private'))
					$values['private']=1;
				$values['reply']=$this->input->post('reply')?$this->input->post('reply'):0;
			if ($this->input->post('id') === NULL)
			{
				$this->query_model->add_query($values);
				echo "query add";
			}
			else
			{
				$values['id']=$this->input->post('id');
				$this->query_model->update_query($values['id'],$values);
			}
			redirect('comments');
		}

		$data = array(
			'all_assignments' => $this->assignment_model->all_assignments(),
			'query_edit' => $this->query_edit
		);

		if ($this->query_edit !== FALSE)
			$data['title'] = 'Edit Query';

		$this->twig->display('pages/admin/add_chat.twig', $data);

	}


	// ------------------------------------------------------------------------

	public function edit($query_id = FALSE)
	{
		if ($this->user->level <= 0) // permission denied
			show_404();
		if ($query_id === FALSE || ! is_numeric($query_id))
			show_404();
		$this->query_edit = $this->query_model->get_query($query_id);
		$this->add();
	}


	// ------------------------------------------------------------------------
	public function toggle()
	{
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($this->user->level <= 0) // permission denied
			$json_result = array('done' => 0, 'message' => 'Permission Denied');
		elseif ($this->input->post('id') === NULL)
			$json_result = array('done' => 0, 'message' => 'Input Error');
		else
		{
			$values['id']=$this->input->post('id');
			$values['private']=$this->input->post('ch');
			$this->query_model->update_query($this->input->post('id'),$values);
			$json_result = array('done' => 1);
		}

		$this->output->set_header('Content-Type: application/json; charset=utf-8');
		echo json_encode($json_result);
	}
	public function delete()
	{
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($this->user->level <= 0) // permission denied
			$json_result = array('done' => 0, 'message' => 'Permission Denied');
		elseif ($this->input->post('id') === NULL)
			$json_result = array('done' => 0, 'message' => 'Input Error');
		else
		{
			$this->query_model->delete_query($this->input->post('id'));
			$json_result = array('done' => 1);
		}

		$this->output->set_header('Content-Type: application/json; charset=utf-8');
		echo json_encode($json_result);
	}


	// ------------------------------------------------------------------------

	public function submitcmnt()
		{
			if ( ! $this->input->is_ajax_request() )
				show_404();
			else
			{
				$values['title']=$this->input->post('title');
				$values['message']=$this->input->post('text');
				$values['assignment']=$this->user->selected_assignment['id'];
				$values['problem']=$this->input->post('problem');
				$values['source']=$this->user_model->username_to_user_id($this->user->username);
				if($this->user->level==0)
				$values['private']=1;
				else $values['private']=0;
				$values['time']=shj_now_str();
				$values['reply']=0;
				$this->query_model->add_query($values);
				$json_result = array('done' => 1);
			}

			$this->output->set_header('Content-Type: application/json; charset=utf-8');
			echo json_encode($json_result);
		}

	//---------------------------------------------------------------------------
	public function reply($query_id = FALSE)
	{
		if ($this->user->level <= 0) // permission denied
			show_404();
		if ($query_id === FALSE || ! is_numeric($query_id))
			show_404();
		$values = $this->query_model->get_query($query_id);
		$this->quert_edit['private']=0;
		$this->query_edit['reply']=$query_id;
		$this->query_edit['problem']=$values['problem'];
		$this->query_edit['private']=0;
		$this->query_edit['title']=$values['title'];
		//print_r($this->query_edit);
		$this->add();
		/*$values['private']=0;
		$this->query_model->update_query($values['id'],$values);*/
	}
	public function check()
	{
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($this->user->level <= 0) // permission denied
			exit('no_new_comments');
		$time  = $this->input->post('time');
		if ($time === NULL)
			exit('error');
		if ($this->query_model->have_new_comments($this->user->selected_assignment['id'],strtotime($time)))
			exit('new_comment');
		exit("strtotime($time)");
		}
}