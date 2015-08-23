<?php
/**
 * The Campus Judge
 * @file Mcq.php
 *  author: Shubham vishwakarma <shubhamvishwakarma987@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Mcq extends CI_Controller
{
	private $mcq_edit;

	// ------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')) // if not logged in
			redirect('login');
		$this->load->model('mcq_model');
		$this->mcq_edit=FALSE;
	}

	public function view($problem_id)
	{
		echo "function created";
	}

	/**
	 * Function to add and MCQ Question
	 */
	public function add($assignment_id=NULL)
	{
		if($this->user->level<=1||$assignment_id==NULL) //permission denied
		show_404();
		$all_assignments=$this->assignment_model->all_assignments();
		if(!array_key_exists($assignment_id,$all_assignments))
			show_404();
		$this->form_validation->set_rules("name","Name","required|max_length[98]");
		$this->form_validation->set_rules("correct","Correct option","required|greater_than[0]|less_than[5]");
		$this->form_validation->set_rules("score","Score","required|greater_than[0]");
		$this->form_validation->set_rules("desc","Description","required");
		$newdata=FALSE;
		$message="";
		if($this->form_validation->run())
		{
			$data=array(
				'assignment'=>$assignment_id,
				'name'=>$this->input->post('name'),
				'score'=>$this->input->post('score'),
				'description'=>$this->input->post('desc'),
				'o1'=>$this->input->post('o1'),
				'o2'=>$this->input->post('o2'),
				'o3'=>$this->input->post('o3'),
				'o4'=>$this->input->post('o4'),
				'correct'=>$this->input->post('correct'),
				'star'=>$this->input->post('star')?1:0
				);
			if(($this->input->post('id'))===NULL)
			{
				$this->mcq_model->add($data);
				$newdata=TRUE;
				$message="Problem Added Successfully, Add next problem here";
			}
			else
			{
					$this->mcq_model->edit($assignment_id,$this->input->post('id'),$data);
					redirect("assignments/edit/$assignment_id");
			}
		}
		if($this->mcq_edit)
		{
			$this->mcq_edit=$this->mcq_model->getproblem($assignment_id,$this->mcq_edit);
			if($this->mcq_edit)
				$newdata=TRUE;
			else
				show_404();
		}
		$data=array('all_assignments'=>$all_assignments,
			'edit'=>$this->mcq_edit,
			'id'=>$assignment_id,
			'newdata'=>$newdata,
			'message'=>$message
			);
		$this->twig->display("pages/admin/add_mcq.twig",$data);

	}

	/**
	 * Function to edit a MCQ 
	 */
	public function edit($assignment_id,$problem_id=NULL)
	{
		if($problem_id==NULL)show_404();
		$this->mcq_edit=$problem_id;
		$this->add($assignment_id);
	}
	/**
	 * Function to delete an assignment
	 */
	public function remove($assignment_id=NULL,$problem_id=NULL)
	{
		if($this->user->level<=1||$assignment_id==NULL||$problem_id==NULL) //permission denied
		show_404();
		$this->mcq_model->drop($assignment_id,$problem_id);
		$data=array('all_assignments'=>$this->assignment_model->all_assignments(),
			'message'=>"The problem has been successfully deleted",
			'redirect'=>"assignments/edit/$assignment_id",
			'title'=>"Problem Deleted"
			);
		$this->twig->display("pages/message.twig",$data);
	}
	/**
	 * Function to backup data
	 */
	public function backup($assignment_id=NULL)
	{
		if($this->user->level<=1||$assignment_id==NULL) //permission denied
		show_404();
		$allmcqs=$this->mcq_model->getallmcq($assignment_id,FALSE);
		$this->load->helper('download');
		force_download("mcqproblems.json",json_encode($allmcqs),TRUE);
	}
}
