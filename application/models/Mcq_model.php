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
	public function batchadd($data)
	{
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
			$data=array('id','name','score','correct','star');
		else
			$data=array('name','score','description','o1','o2','o3','o4','correct','star');
		return $this->db->select($data)
						->get_where('mcqproblems',array("assignment"=>$assignment_id))
						->result_array();
	}
	//------------------------------
	public function drop($assignment_id,$problem_id)
	{
		$this->db->delete('mcqproblems', array('id' => $problem_id,'assignment'=>$assignment_id));
	}

}
