<?php
/**
 * Sharif Judge online judge
 * @file Notifications_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Query_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all queries
	 */
	public function get_all_queries($assignment_id)
	{
		return $this->db
					->select('query.*,users.username')
					->where('assignment',$assignment_id)
					->from('query')
					->join('users','users.id = query.source')
					->order_by('query.id','desc')
					->get()
					->result_array();
	}

	// ------------------------------------------------------------------------
/**
 * returns all public queries
 */
	public function get_public_queries($assignment_id,$problem_id)
	{
		return $this->db
					->select('query.*,users.username')
					->where(array('assignment'=>$assignment_id,'private'=> 0,'problem'=> "$problem_id"))
					->from('query')
					->join('users','users.id = query.source')
					->order_by('query.id','desc')
					->get()
					->result_array();
	}
	// ------------------------------------------------------------------------

/**
 * Add a query to database
 * 
 */
	public function add_query($values)
	{
		//$now = shj_now_str();
		$this->db->insert('query',$values);
	}
	// ------------------------------------------------------------------------


	/**
	 * Update (edit) a query
	 */
	public function update_query($id,$values)
	{
		$this->db
			->where('id', $id)
			->update('query',$values);
	}


	// ------------------------------------------------------------------------


	/**
     * Delete a query
	 */
	public function delete_query($id)
	{
		$this->db->delete('query', array('id' => $id));
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns a query
	 */
	public function get_query($query_id)
	{
		$query = $this->db->get_where('query', array('id' => $query_id));
		if ($query->num_rows() != 1)
			return FALSE;
		return $query->row_array();
	}
	//---
/**
 * check for new comments
 */
	public function have_new_comments($assignment_id,$time)
	{
		$notifs = $this->db->select_max('time')->where('assignment',$assignment_id)->get('query')->result_array();
		foreach ($notifs as $notif) {
			if (strtotime($notif['time']) > $time)
				return TRUE;
		}
		return FALSE;
	}

}