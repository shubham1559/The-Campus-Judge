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
		$docss[0]=array();
		$docss[1]=array();

		$docss[2]=array('Add Assignment'=>"add_assignment.html",
				"Sample Assignment"=>"sample_assignment.html",
				"Detect similar codes"=>"moss.html",
				"Tests Structure" =>"tests_structure.html",
				);

		$docss[3]=array('Users'=>"users.html",
			'Clean urls'=>"clean_urls.html",
			'Installation'=>"installation.html",
			'Settings'=>"settings.html",
			'Sandboxing'=>"sandboxing.html",
			'Security'=>"security.html",
			'Shield'=>"shield.html");
		$baseaddr="assets/docs/";
		$filename="index.html";
		$level= $this->user->level;
		$docs=[];
		for($i=0;$i<=$level;$i++)
			$docs=array_merge($docs,$docss[$i]);
		if($doc_id===NULL)
		{		
			$data = array(
					'all_assignments' => $this->assignment_model->all_assignments(),
					'docs' =>array_keys($docs),
				);
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
	}


}