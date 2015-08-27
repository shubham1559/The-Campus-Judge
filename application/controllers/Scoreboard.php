<?php
/**
 * Sharif Judge online judge
 * @file Scoreboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Scoreboard extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();
		if ($this->input->is_cli_request())
			return;
		if ( ! $this->session->userdata('logged_in')) // if not logged in
			redirect('login');
	}


	// ------------------------------------------------------------------------


	public function index()
	{

		$this->load->model('scoreboard_model');

		$data = array(
			'all_assignments' => $this->assignment_model->all_assignments(),
			'scoreboard' => $this->scoreboard_model->get_scoreboard($this->user->selected_assignment['id'])
		);

		$this->twig->display('pages/scoreboard.twig', $data);
	}
	//-----------------------
	public function scoreboard_helper()
	{
		if($this->user->level<=1)
			show_404();
		$data = array(
			'all_assignments' => $this->assignment_model->all_assignments(),
		);
		$this->form_validation->set_rules('assignment_id', 'Assignment id', 'required|integer');
		if($this->form_validation->run())
		{
			$assignment_id=$this->input->post('assignment_id');
			$this->load->model('scoreboard_model');
			$this->scoreboard_model->update_scoreboard($assignment_id);
			$data['msg'][]="Scoreboard Updated for Assignment: $assignment_id";
		}
		$this->twig->display("pages/admin/generate_scoreboard.twig",$data);
	}


}