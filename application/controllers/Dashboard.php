<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user')) redirect('login');
    }

    public function index()
    {
        $user = (object) $this->session->userdata('user');
        $filters = $user->nama_role === 'User/Pelapor' ? array('user_id' => $user->id) : array();
        $data = array('summary' => $this->Ticket_model->summary(), 'tickets' => $this->Ticket_model->list($filters));
        $this->load->view('layouts/header', array('title' => 'Dashboard'));
        $this->load->view('dashboard/index', $data);
        $this->load->view('layouts/footer');
    }
}
