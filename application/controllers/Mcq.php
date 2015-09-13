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

	/**
	 * Function to add MCQ Question
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
		$this->form_validation->set_rules("negative","Negative Score","required|greater_than_equal_to[0]");
		$this->form_validation->set_rules("desc","Description","required");
		$newdata=FALSE;
		$message="";
		if($this->form_validation->run())
		{
			$data=array(
				'assignment'=>$assignment_id,
				'name'=>$this->input->post('name'),
				'score'=>$this->input->post('score'),
				'negative'=>$this->input->post('negative'),
				'description'=>$this->input->post('desc'),
				'o1'=>$this->input->post('o1'),
				'o2'=>$this->input->post('o2'),
				'o3'=>$this->input->post('o3'),
				'o4'=>$this->input->post('o4'),
				'correct'=>$this->input->post('correct'),
				'star'=>$this->input->post('star')?1:0
				);
			if(($this->input->post('id'))==NULL)			//problems added
			{
				$this->mcq_model->add($data);
				$newdata=TRUE;
				$message="Problem Added Successfully, Add next problem here";
				$this->mcq_edit['negative']=$data['negative'];		//cache current score and negative score
				$this->mcq_edit['score']=$data['score'];			//If called from $this->edit function $newdata will be false
			}
			else
			{
				$problem_id=$this->input->post('id');
				$this->mcq_model->edit($assignment_id,$problem_id,$data);
				redirect("mcq/view/$assignment_id/$problem_id");
			}
		}
		if($this->mcq_edit&&!$newdata)			//If called from $this->edit function 
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
	 * Function to display a question, This is only to preview the question 
	 */
	public function view($assignment_id=NULL,$problem_id=NULL)
	{
		if($this->user->level<=1||$assignment_id==NULL||$problem_id==NULL) //permission denied
		show_404();
		$problem=$this->mcq_model->getproblem($assignment_id,$problem_id);
			if($problem)
				{
					$this->load->library('parsedown');
					$problem['description']=$this->parsedown->parse($problem['description']);
					$problem['o1']=$this->parsedown->parse($problem['o1']);
					$problem['o2']=$this->parsedown->parse($problem['o2']);
					$problem['o3']=$this->parsedown->parse($problem['o3']);
					$problem['o4']=$this->parsedown->parse($problem['o4']);
					$data=array('all_assignments'=>$this->assignment_model->all_assignments(),
							'problem'=>$problem,
				);
					$this->twig->display("pages/admin/show_mcq.twig",$data);
				}
			else
				show_404();
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
	/**
	 * Function to create a public json file
	 */
	public function create_assignment()
	{
		if($this->user->level<=1||!$this->input->is_ajax_request() )//permission denied
			show_404();
		$assignment_id=$this->input->post('assignment');
		$assignments_root = rtrim($this->settings_model->get_setting('assignments_root'), '/');
		$mcqpath="$assignments_root/assignment_{$assignment_id}/mcq";
		$this->mcq_model->generate($assignment_id,$mcqpath);
		exit("Success");
	}
	/**
	 * Function to return mcqproblems for assignments
	 *
	 */
	public function public_assignments()
	{
		if(!$this->input->is_ajax_request())
			show_404();
		$assignment_id=$this->input->post('assignment');
		if($assignment_id==NULL)
			show_404();
		$assignment = $this->assignment_model->assignment_info($assignment_id);
		if($assignment['id']==0)
			show_404();
		//if assignment not started the donot show any data of the assignment
		$data[2]="started";
		if(shj_now()<strtotime($assignment['start_time']))
			$data[2]="Yet to start";
		if(shj_now() > strtotime($assignment['finish_time'])+$assignment['extra_time'])
			$data[2]="ended";
		if($this->user->level == 0 && ! $assignment['open'])
			$data[2]="close";
		if(!$this->assignment_model->is_participant($assignment['participants'], $this->user->username))
		{
			$data[2]="not valid";
		}
		if($this->user->level==0&&shj_now() < strtotime($assignment['start_time']))
		{
			show_404();
		}
		elseif($assignment['public']&& shj_now()>strtotime($assignment['finish_time'])+$assignment['extra_time'])
			{
				$filename="mcq_answ.json";
				$data[2]="public";
			}
		else
			$filename="mcq_without_answer.json";
		$assignments_root = rtrim($this->settings_model->get_setting('assignments_root'), '/');
		$filename="$assignments_root/assignment_{$assignment_id}/mcq/$filename";
		if(!file_exists($filename))
			show_404();
		$data[0]=file_get_contents($filename);
		if($data[0]===FALSE)show_404();
		$data[1]=($this->mcq_model->get_responses($assignment_id,$this->user->username));
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
	/**
	 * Function to add a response for a problems
	 */
	public function submit_response()
	{
		if(!$this->input->is_ajax_request())
			show_404();
		$assignment_id=$this->input->post('assignment');
		if($assignment_id==0)
			show_404();
		$assignment_info=$this->assignment_model->assignment_time($assignment_id);
		if(shj_now() < strtotime($assignment_info['start_time']))
			exit("Assignment Not started yet");
		if(shj_now() > strtotime($assignment_info['finish_time'])+$assignment_info['extra_time'])
			exit("Assignment Finished");
		if(! $this->assignment_model->is_participant($assignment_info['participants'], $this->user->username))
			exit("You are not a valid participant");
		if($this->user->level == 0 && ! $assignment_info['open'])
			exit("Assignment is closed");
		$username=$this->user->username;
		$this->form_validation->set_rules('response','Response','required|greater_than[0]|less_than[5]');
		$this->form_validation->set_rules('id',"Problem Id",'required');
		if($this->form_validation->run())
		{
			$data=array('assignment'=>$assignment_id,
				'username'=>$username,
				'id'=>$this->input->post('id')
				);
			$response=$data;		//data with response
			$response['response']=$this->input->post('response');
			$this->mcq_model->add_response($data,$response);
			exit("Success");
		}else show_404();
	}
	/**
	 * Function to delete a response
	 */
	public function delete_response()
	{
		if(!$this->input->is_ajax_request())
			show_404();
		$assignment_id=$this->input->post('assignment');
		if($assignment_id==0)
			show_404();
		$assignment_info=$this->assignment_model->assignment_time($assignment_id);
		if(shj_now() < strtotime($assignment_info['start_time']))
			exit("Assignment Not started yet");
		if(shj_now() > strtotime($assignment_info['finish_time'])+$assignment_info['extra_time'])
			exit("Assignment Finished");
		if(! $this->assignment_model->is_participant($assignment_info['participants'], $this->user->username))
			exit("You are not a valid participant");
		if($this->user->level == 0 && ! $assignment_info['open'])
			exit("Assignment is closed");
		$username=$this->user->username;
		$this->form_validation->set_rules('id',"Problem Id",'required');
		if($this->form_validation->run())
		{
			$data=array('assignment'=>$assignment_id,
				'username'=>$username,
				'id'=>$this->input->post('id')
				);
			$this->mcq_model->delete_response($data);
			exit("Success");
		}else show_404();
	}
	/**
	 * The function is polled for new updates aboud the assignment.
	 * @return boolean [description]
	 */
	public function is_update()
	{
		if(!$this->input->is_ajax_request())
			show_404();
		$assignment_id=$this->input->post('assignment');
		if($assignment_id==0)
			show_404();
		$assignments_root = rtrim($this->settings_model->get_setting('assignments_root'), '/');
		$assignment_info=$this->assignment_model->assignment_time($assignment_id);
		if(shj_now() > strtotime($assignment_info['finish_time'])+$assignment_info['extra_time'])
			exit("Assignment Finished");
		$filename="$assignments_root/assignment_{$assignment_id}/mcq/mcq_without_answer.json";
		exit(date("Y m d H:i:s", filemtime($filename)));
	}
	public function usermcq($assignment_id=NULL)
	{
		if($this->user->level<=1) //permission denied
		show_404();
		if ($assignment_id === NULL)
			$assignment_id = $this->user->selected_assignment['id'];
		if ($assignment_id == 0)
			show_error('No assignment selected.');
		$mcqproblems=$this->mcq_model->getallmcq($assignment_id);
		if(!$mcqproblems)show_404();
		$data = array(
			'all_assignments' => $this->assignment_model->all_assignments(),
			'mcqproblems'=>$mcqproblems,
			'assignment'=>$assignment_id,
		);
		$query=$this->mcq_model->get_responses($assignment_id);
			$data['stats']=[];
			foreach ($mcqproblems as $key) {
				$data['stats'][$key['id']]=$key;
			}
			$data['userstats']=[];
			foreach ($query as $key) {
				$data['userstats'][$key['username']][$key['id']]['response']=$key['response'];
				$data['userstats'][$key['username']]['username']=$key['username'];
			}
			foreach ($data['userstats'] as &$response) {
				$response['correct']=0;
				$response['score']=0;
				$response['star']=0;
				$response['incorrect']=0;
				foreach ($data['stats'] as $key) {
					if(isset($response[$key['id']]))
						{
							if($key['correct']==$response[$key['id']]['response'])
								{
									$response[$key['id']]['class']="correct";
									$response['correct']++;
									$response['score']+=$key['score'];
									$response['star']+=$key['star'];
								}
							else
								{
									$response[$key['id']]['class']="wrong";
									$response['incorrect']++;
									$response['score']-=$key['negative'];
								}
						}
						else $response[$key['id']]['class']="";
				}
			}
			//print_r($data['userstats'])	;
			$this->twig->display('pages/admin/mcquserstats.twig', $data);
	}
	public function showstats($assignment_id=NULL,$username=NULL)
	{
		if($this->user->level<=1) //permission denied
		show_404();
		if ($assignment_id === NULL)
			$assignment_id = $this->user->selected_assignment['id'];
		if ($assignment_id == 0)
			show_error('No assignment selected.');
		$mcqproblems=$this->mcq_model->getallmcq($assignment_id);
		if(!$mcqproblems)show_404();
		$data = array(
			'all_assignments' => $this->assignment_model->all_assignments(),
			'mcqproblems'=>$mcqproblems,
		);
		if($username==NULL)
		{
			$query=$this->mcq_model->getstats($assignment_id);
			$stats=[];
			foreach ($query as $key) {
				$stats[$key['id']][$key['response']]=$key['cnt'];
			}
			$data['stats']=$stats;
			$data['username']=NULL;
		}
		else{
			$query=$this->mcq_model->get_responses($assignment_id,$username);
			if(!$query)show_404();
			$stats=[];
			foreach ($query as $key) {
				$stats[$key['id']]=$key['response'];
			}
			$data['username']=$username;
			$data['stats']=$stats;
		}
		$data['total_problems']=0;;
		$data['total_score']=0;
		$data['correct']=0;
		$data['incorrect']=0;
		$data['stars']=0;
		$data['final_score']=0;
		foreach ($mcqproblems as $key) {
			$data['color'][$key['id']]=array('','','','','');
			if($data['username']&&isset($stats[$key['id']]))
			$data['color'][$key['id']][$stats[$key['id']]]="wrong";
			if(!$data['username']||(isset($stats[$key['id']])&&$key['correct']==$stats[$key['id']]))
			$data['color'][$key['id']][$key['correct']]="correct";
			$data['total_problems']++;
			$data['total_score']+=$key['score'];
			if($data['username'])
			{
				if(isset($stats[$key['id']]))
				{
					if($key['correct']==$stats[$key['id']])
					{
						$data['correct']++;
						$data['final_score']+=$key['score'];
						$data['score'][$key['id']]=$key['score'];
						$data['stars']+=$key['star'];
					}
					else{
						$data['incorrect']++;
						$data['final_score']-=$key['negative'];
						$data['score'][$key['id']]=-$key['negative'];
					}
				}else
				$data['score'][$key['id']]=0;
			}
		}
		$this->twig->display('pages/admin/mcqstats.twig', $data);
	}
}
