<?php
/**
 * Sharif Judge online judge
 * @file Problems.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Problems extends CI_Controller
{

	private $all_assignments;


	// ------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')) // if not logged in
			redirect('login');

		$this->all_assignments = $this->assignment_model->all_assignments();
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays detail description of given problem
	 *
	 * @param int $assignment_id
	 * @param int $problem_id
	 */
	public function index($assignment_id = NULL, $problem_id = NULL)
	{

		// If no assignment is given, use selected assignment
		
		if ($assignment_id === NULL)
			$assignment_id = $this->user->selected_assignment['id'];
		if ($assignment_id == 0)
			show_error('No assignment selected.');
		$this->load->model("query_model");

		$assignment = $this->assignment_model->assignment_info($assignment_id);
		$comments=$this->query_model->get_public_queries($assignment_id,$problem_id);
		$data = array(
			'all_assignments' => $this->all_assignments,
			'all_problems' => $this->assignment_model->all_problems($assignment_id),
			'description_assignment' => $assignment,
			'can_submit' => TRUE,
			'comments'	=>$comments,
		);
			if ( $assignment['id'] == 0
			OR ( $this->user->level == 0 && ! $assignment['open'] )
			OR shj_now() < strtotime($assignment['start_time'])
			OR shj_now() > strtotime($assignment['finish_time'])+$assignment['extra_time'] // deadline = finish_time + extra_time
			OR ! $this->assignment_model->is_participant($assignment['participants'], $this->user->username)
		)
			$data['can_submit'] = FALSE;
			//if assignment not started the donot show any data of the assignment
			if($this->user->level==0&&shj_now() < strtotime($assignment['start_time']))
			{
				$this->twig->display('pages/notstarted.twig',array('all_assignments'=>$this->all_assignments));
				return; 
			}
			//if no problem given show the summary of assignment
			if($problem_id==NULL)
			{
				$this->load->model('submit_model');
				$problems=$this->assignment_model->all_problems($assignment_id);
				$summ=$this->submit_model->count_all_solved($assignment_id,$this->user->username);
				//print_r($summ);
				foreach($problems as $problem)
				{
							if(isset($summ[$problem['id']]['avg']))
							$problems[$problem['id']]['avg']=$summ[$problem['id']]['avg']/10000;
						else $problems[$problem['id']]['avg']=0;
						if(isset($summ[$problem['id']]['cnt']))
							$problems[$problem['id']]['cnt']=$summ[$problem['id']]['cnt'];
						else $problems[$problem['id']]['cnt']=0;
						if(isset($summ[$problem['id']]['col']))
						{
							switch ($summ[$problem['id']]['col']) {
								case 'SCORE':
									$problems[$problem['id']]['col']="solved";
									break;
								case 'Uploaded':
									$problems[$problem['id']]['col']="uploaded";
									break;
								default:
									$problems[$problem['id']]['col']="wrong";
									break;
							}
						}
						else $problems[$problem['id']]['col']='';
					$problems[$problem['id']]['avg']= sprintf('%0.2f', $problems[$problem['id']]['avg']);
				}
				$data = array(
					'all_assignments' => $this->all_assignments,
					'problems' => $problems,
				);
				$this->twig->display('pages/allproblems.twig', $data);
				return;
			}

		if ( ! is_numeric($problem_id) || $problem_id < 1 || $problem_id > $data['description_assignment']['problems'])
			show_404();

		$languages = explode(',',$data['all_problems'][$problem_id]['allowed_languages']);

		$assignments_root = rtrim($this->settings_model->get_setting('assignments_root'),'/');
		$problem_dir = "$assignments_root/assignment_{$assignment_id}/p{$problem_id}";
		$data['problem'] = array(
			'id' => $problem_id,
			'description' => '<p>Description not found</p>',
			'allowed_languages' => $languages,
			'has_pdf' => glob("$problem_dir/*.pdf") != FALSE
		);

		$path = "$problem_dir/desc.html";
		if (file_exists($path))
			$data['problem']['description'] = file_get_contents($path);
		$this->twig->display('pages/problems.twig', $data);
	}
	/**
	 * It is used to display summary of the selected assignment
	 */
	//---------------------------------------------------------------------------
/*	public function summary()
	{
		// If no assignment is given, use selected assignment
		$this->load->model('submit_model');
		if ($assignment_id === NULL)
			$assignment_id = $this->user->selected_assignment['id'];
		if ($assignment_id == 0)
			show_error('No assignment selected.');
		$assignment = $this->assignment_model->assignment_info($assignment_id);
		$problems=$this->assignment_model->all_problems($assignment_id);
		$summ=$this->submit_model->count_all_solved($assignment_id,$this->user->username);
		//print_r($summ);
		foreach($problems as $problem)
		{
					if(isset($summ[$problem['id']]['avg']))
					$problems[$problem['id']]['avg']=$summ[$problem['id']]['avg']/10000;
				else $problems[$problem['id']]['avg']=0;
				if(isset($summ[$problem['id']]['cnt']))
					$problems[$problem['id']]['cnt']=$summ[$problem['id']]['cnt'];
				else $problems[$problem['id']]['cnt']=0;
				if(isset($summ[$problem['id']]['col']))
					$problems[$problem['id']]['col']=$summ[$problem['id']]['col']=="SCORE"?"solved":"wrong";
				else $problems[$problem['id']]['col']='';
			$problems[$problem['id']]['avg']= sprintf('%0.2f', $problems[$problem['id']]['avg']);
		}
		$data = array(
			'all_assignments' => $this->all_assignments,
			'problems' => $problems,
		);
		$this->twig->display('pages/allproblems.twig', $data);
	}
*/
	// ------------------------------------------------------------------------


	/**
	 * Edit problem description as html/markdown
	 *
	 * $type can be 'md', 'html', or 'plain'
	 *
	 * @param string $type
	 * @param int $assignment_id
	 * @param int $problem_id
	 */
	public function edit($type = 'md', $assignment_id = NULL, $problem_id = 1)
	{
		if ($type !== 'html' && $type !== 'md' && $type !== 'plain')
			show_404();

		if ($this->user->level <= 1)
			show_404();

		switch($type)
		{
			case 'html':
				$ext = 'html'; break;
			case 'md':
				$ext = 'md'; break;
			case 'plain':
				$ext = 'html'; break;
		}

		if ($assignment_id === NULL)
			$assignment_id = $this->user->selected_assignment['id'];
		if ($assignment_id == 0)
			show_error('No assignment selected.');

		$data = array(
			'all_assignments' => $this->assignment_model->all_assignments(),
			'description_assignment' => $this->assignment_model->assignment_info($assignment_id),
		);

		if ( ! is_numeric($problem_id) || $problem_id < 1 || $problem_id > $data['description_assignment']['problems'])
			show_404();

		$this->form_validation->set_rules('text', 'text' ,''); /* todo: xss clean */
		if ($this->form_validation->run())
		{
			$this->assignment_model->save_problem_description($assignment_id, $problem_id, $this->input->post('text'), $ext);
			redirect('problems/'.$assignment_id.'/'.$problem_id);
		}

		$data['problem'] = array(
			'id' => $problem_id,
			'description' => ''
		);

		$path = rtrim($this->settings_model->get_setting('assignments_root'),'/')."/assignment_{$assignment_id}/p{$problem_id}/desc.".$ext;
		if (file_exists($path))
			$data['problem']['description'] = file_get_contents($path);


		$this->twig->display('pages/admin/edit_problem_'.$type.'.twig', $data);

	}
	public function showmcq()
	{
		$data=array('all_assignments' => $this->assignment_model->all_assignments());
		$this->twig->display("pages/showmcq.twig",$data);
	}

}