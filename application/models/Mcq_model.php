<?php
/**
 *The Campus Judge
 *Mcq_modeal.php
 * author: Shubham vishwakarma <shubhamvishwakarma987@gmail.com>
 * This class is used for all database operations for mcq problems
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Mcq_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	//------------------------------
	public function add($data)
	{
		$this->db->insert('mcqproblems',$data);
	}
	//----------------------
	public function batchadd($data,$assignment_id)
	{
		$this->db->delete('mcqproblems', array('assignment'=>$assignment_id));
		$this->db->insert_batch('mcqproblems',$data);
	}
	//--------------------------
	public function getproblem($assignment_id,$problem_id)
	{
		$result= $this->db->get_where('mcqproblems',array('assignment'=>$assignment_id,'id'=>$problem_id))
						->row_array();
		return $result?$result:FALSE;
	}

	//-------------------------------------
	public function edit($assignment_id,$problem_id,$data)
	{
		$this->db->where(array('assignment'=>$assignment_id,'id'=>$problem_id))
			->update('mcqproblems',$data);
	}
	//---------------------------
	public function getallmcq($assignment_id,$bool=TRUE)
	{
		if($bool)
			$data=array('id','name','score','negative','correct','star');
		else
			$data=array('name','score','negative','description','o1','o2','o3','o4','correct','star');
		return $this->db->select($data)
						->get_where('mcqproblems',array("assignment"=>$assignment_id))
						->result_array();
	}
	//------------------------------
	public function drop($assignment_id,$problem_id)
	{
		$this->db->delete('mcqproblems', array('id' => $problem_id,'assignment'=>$assignment_id));
		$this->db->delete('mcqresponse', array('id' => $problem_id,'assignment'=>$assignment_id));
	}
	//--------------------------------
	/**
	 * This function is used to generate json files for the mcq problems
	 * @param  Path to mcq folder has the path of the mcq folder of some assignment without a trailing slash
	 */
	public function generate($assignment_id,$path_to_mcq)
	{
		if(!file_exists($path_to_mcq))
			mkdir($path_to_mcq,0700);
		array_map('unlink', glob("$path_to_mcq/*"));
		$data=$this->db->select(array('id','name','score','negative','description','o1','o2','o3','o4','star','correct'))
							->get_where('mcqproblems',array("assignment"=>$assignment_id))
							->result_array();
			$this->load->library('parsedown');
		foreach ($data as &$element) {
			$element['description']=$this->parsedown->parse($element['description']);
			$element['o1']=$this->parsedown->parse($element['o1']);
			$element['o2']=$this->parsedown->parse($element['o2']);
			$element['o3']=$this->parsedown->parse($element['o3']);
			$element['o4']=$this->parsedown->parse($element['o4']);
			}
		file_put_contents("$path_to_mcq/mcq_answ.json",json_encode($data));
		foreach ($data as &$element) {
		$element = array_slice($element, 0, 10);
			}
		file_put_contents("$path_to_mcq/mcq_without_answer.json",json_encode($data));
	}
	/**
	 *
	 */
	public function add_response($data,$response)
	{
		$this->db->insert('mcqresponse',$response);
	}
	//------------
	public function get_responses($assignment_id,$username)
	{
		return $this->db->select(array('id','response'))
		->get_where('mcqresponse',array('assignment'=>$assignment_id,'username'=>$username))
		->result_array();
	}
	//------------------------
	public function delete_response($data)
	{
		$this->db->delete('mcqresponse',$data);
	}
}
