<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Approval extends CI_Controller
{
    private $user;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user')) redirect('login');
        $this->user = (object) $this->session->userdata('user');
        if ($this->user->nama_role !== 'Atasan IT') show_error('Halaman ini khusus Atasan IT.', 403);
    }

    public function index()
    {
        $this->load->view('layouts/header', array('title' => 'Persetujuan Tiket'));
        $this->load->view('approval/index', array('tickets' => $this->Ticket_model->list(array('status' => 4))));
        $this->load->view('layouts/footer');
    }

    public function decide($id)
    {
        $ticket = $this->Ticket_model->find($id);
        if (!$ticket || $ticket->status_id != 4) show_error('Tiket tidak menunggu persetujuan.');
        $approved = $this->input->post('decision') === 'approve';
        $this->db->insert('ticket_approval', array('ticket_id' => $id, 'atasan_id' => $this->user->id, 'kode_barcode_approve' => ticket_code('APR', $id), 'waktu_approve' => date('Y-m-d H:i:s'), 'status' => $approved ? 'approve' : 'reject', 'catatan' => $this->input->post('catatan', TRUE)));
        $this->Ticket_model->change_status($ticket, $approved ? 5 : 7, $this->user->id);
        $this->session->set_flashdata('success', $approved ? 'Tiket disetujui.' : 'Tiket dikembalikan ke teknisi.');
        redirect('approval');
    }
}
