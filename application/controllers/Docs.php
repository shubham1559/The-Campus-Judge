<?php
/**
 * Sharif Judge online judge
 * @file Scoreboard.php
 * author: Shubham vishwakarma <shubhamvishwakarma987@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Docs extends CI_Controller
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


	public function index($doc_id=NULL)
	{
		$docs=array(
			'sample' => "index.html");
		$baseaddr="assets/docs/";
		$filename="index.html";
		if($doc_id===NULL)
		{		
			$data = array(
					'all_assignments' => $this->assignment_model->all_assignments(),
					'docs' =>array_keys($docs),
				);
				//echo file_get_contents($baseaddr.$filename);
				$this->twig->display('pages/docslist.twig', $data);
			}
		else{
			if(array_key_exists($doc_id,$docs))
			{
					$data = array(
					'all_assignments' => $this->assignment_model->all_assignments(),
					'page' =>file_get_contents($baseaddr.$docs[$doc_id]),
				);
				//echo file_get_contents($baseaddr.$filename);
				$this->twig->display('pages/docview.twig', $data);
			}
			else
			show_404();
		}
		//print_r($docs);
	}


}