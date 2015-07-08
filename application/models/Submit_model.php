<?php
/**
 * Sharif Judge online judge
 * @file Submit_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Submit_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns table row for a specific submission
	 */
	public function get_submission($username, $assignment, $problem, $submit_id)
	{
		$query = $this->db->get_where('submissions',
			array(
				'username'=>$username,
				'assignment'=>$assignment,
				'problem'=>$problem,
				'submit_id'=>$submit_id
			)
		);
		if($query->num_rows()!=1)
			return FALSE;
		return $query->row_array();
	}


	// ------------------------------------------------------------------------


	public function get_final_submissions($assignment_id, $user_level, $username, $page_number = NULL, $filter_user = NULL, $filter_problem = NULL)
	{
		$arr['assignment'] = $assignment_id;
		$arr['is_final'] = 1;
		if ($user_level === 0)// students can only get final submissions of themselves
			$arr['username']=$username;
		elseif ($filter_user !== NULL)
			$arr['username'] = $filter_user;
		if ($filter_problem !== NULL)
			$arr['problem'] = $filter_problem;
		if ($page_number === NULL)
			return $this->db->order_by('username asc, problem asc')->get_where('submissions', $arr)->result_array();
		else
		{
			$per_page = $this->settings_model->get_setting('results_per_page_final');
			if ($per_page == 0)
				return $this->db->order_by('username asc, problem asc')->get_where('submissions', $arr)->result_array();
			else
				return $this->db->order_by('username asc, problem asc')->limit($per_page,($page_number-1)*$per_page)->get_where('submissions', $arr)->result_array();
		}

	}


	// ------------------------------------------------------------------------


	public function get_all_submissions($assignment_id, $user_level, $username, $page_number = NULL, $filter_user = NULL, $filter_problem = NULL)
	{
		$arr['assignment']=$assignment_id;
		if ($user_level === 0)
			$arr['username']=$username;
		elseif ($filter_user !== NULL)
			$arr['username'] = $filter_user;
		if ($filter_problem !== NULL)
			$arr['problem'] = $filter_problem;
		if ($page_number === NULL)
			return $this->db->order_by('submit_id','desc')->get_where('submissions', $arr)->result_array();
		else
		{
			$per_page = $this->settings_model->get_setting('results_per_page_all');
			if ($per_page == 0)
				return $this->db->order_by('submit_id','desc')->get_where('submissions', $arr)->result_array();
			else
				return $this->db->order_by('submit_id','desc')->limit($per_page,($page_number-1)*$per_page)->get_where('submissions', $arr)->result_array();
		}
	}


	// ------------------------------------------------------------------------


	public function count_final_submissions($assignment_id, $user_level, $username, $filter_user = NULL, $filter_problem = NULL)
	{
		$arr['assignment'] = $assignment_id;
		$arr['is_final'] = 1;
		if ($user_level === 0)
			$arr['username']=$username;
		elseif ($filter_user !== NULL)
			$arr['username'] = $filter_user;
		if ($filter_problem !== NULL)
			$arr['problem'] = $filter_problem;
		return $this->db->where($arr)->count_all_results('submissions');
	}


	// ------------------------------------------------------------------------


	public function count_all_submissions($assignment_id, $user_level, $username, $filter_user = NULL, $filter_problem = NULL)
	{
		$arr['assignment']=$assignment_id;
		if ($user_level === 0)
			$arr['username']=$username;
		elseif ($filter_user !== NULL)
			$arr['username'] = $filter_user;
		if ($filter_problem !== NULL)
			$arr['problem'] = $filter_problem;
		return $this->db->where($arr)->count_all_results('submissions');
	}


	// ------------------------------------------------------------------------


	public function set_final_submission($username, $assignment, $problem, $submit_id)
	{

		$this->db->where(array(
			'is_final' => 1,
			'username' => $username,
			'assignment' => $assignment,
			'problem' => $problem,
		))->update('submissions', array('is_final'=>0));

		$this->db->where(array(
			'username' => $username,
			'assignment' => $assignment,
			'problem' => $problem,
			'submit_id' => $submit_id,
		))->update('submissions', array('is_final'=>1));

		return TRUE;
	}


	// ------------------------------------------------------------------------


	/**
	 * add the result of an "upload only" submit to the database
	 */
	public function add_upload_only($submit_info)
	{

		$this->db->where(array(
			'is_final' => 1,
			'username' => $submit_info['username'],
			'assignment' => $submit_info['assignment'],
			'problem' => $submit_info['problem'],
		))->update('submissions', array('is_final'=>0));

		$submit_info['is_final'] = 1;
		$submit_info['status'] = 'Uploaded';

		$this->db->insert('submissions', $submit_info);

	}
	//-------------------------------------------------------
	//summary of the assignment
	public function count_all_solved($assignment_id,$username)
	{
		$cor= $this->db
				->select("COUNT(DISTINCT(username)) as cnt,problem",FALSE)
				->where(array(
				'assignment' =>$assignment_id,
				'status'=>"SCORE",
			))->group_by("problem")
			->get('submissions')
			->result_array();

			$avg =$this->db
					->select("AVG(pre_score*coefficient)as avg,problem")
					->where(array(
					'assignment' =>$assignment_id,
					))->group_by("problem")
					//->get_compiled_select("submissions");
					->get('submissions')
					->result_array();
			$col=$this->db
					->select("problem,status")
					->where(array(
						'assignment' =>$assignment_id,
						'is_final' =>1,
						'username' =>$username,
						))
					->get('submissions')
					->result_array();
			$val=[];
			foreach($cor as $prob){
				$val[$prob['problem']]['cnt']=$prob['cnt'];
			}
			foreach($avg as $prob){
				$val[$prob['problem']]['avg']=$prob['avg'];
			}
			foreach($col as $prob){
				$val[$prob['problem']]['col']=$prob['status'];
			}
			return $val;	
	}
		/**
	 * this function checks status of a submission 
	 * @param [number] $[submission_id] [<id of submission>]
	 */
	public function get_status($assignment_id,$submission_id,$username)
	{
		return $this->db->select("status")
						->where(array(
							'submit_id'=>$submission_id,
							'assignment'=>$assignment_id,
							'username'=>$username
							))
						->get('submissions')
						->result_array();
	}
}