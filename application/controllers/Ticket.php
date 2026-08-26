<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket extends CI_Controller
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
        $this->load->view('layouts/header', array('title' => 'Tiket'));
        $this->load->view('ticket/index', array('tickets' => $this->Ticket_model->list($filters)));
        $this->load->view('layouts/footer');
    }

    public function create()
    {
        $user = (object) $this->session->userdata('user');
        if ($this->input->method() === 'post') {
            $ticketId = $this->Ticket_model->create(array('user_id' => $user->nama_role === 'User/Pelapor' ? $user->id : NULL, 'nama_pelapor' => $this->input->post('nama_pelapor', TRUE), 'unit_id' => $this->input->post('unit_id', TRUE), 'jenis_keluhan_id' => $this->input->post('jenis_keluhan_id', TRUE), 'jenis_lainnya' => $this->input->post('jenis_lainnya', TRUE), 'deskripsi_aduan' => $this->input->post('deskripsi_aduan', TRUE), 'kelengkapan_barang' => $this->input->post('kelengkapan_barang', TRUE), 'keluhan_pengguna' => $this->input->post('keluhan_pengguna', TRUE), 'source' => $user->nama_role === 'Teknisi' ? 'teknisi' : 'self', 'created_by' => $user->id));
            $this->load->library('notification_lib');
            $this->notification_lib->ticket_created($ticketId);
            $this->session->set_flashdata('success', 'Tiket berhasil dibuat. Notifikasi email telah diproses untuk teknisi.');
            redirect('tickets/'.$ticketId);
        }
        $data = array('units' => $this->db->get('units')->result(), 'types' => $this->db->get('jenis_keluhan')->result());
        $this->load->view('layouts/header', array('title' => 'Buat Tiket'));
        $this->load->view('ticket/form', $data);
        $this->load->view('layouts/footer');
    }

    public function detail($id)
    {
        $ticket = $this->Ticket_model->find($id);
        if (!$ticket) show_404();
        $this->load->view('layouts/header', array('title' => $ticket->no_tiket));
        $this->load->view('ticket/detail', array('ticket' => $ticket));
        $this->load->view('layouts/footer');
    }

    public function confirm($id)
    {
        $user = (object) $this->session->userdata('user');
        $ticket = $this->Ticket_model->find($id);
        if (!$ticket || $ticket->status_id != 5 || $ticket->user_id != $user->id) show_error('Konfirmasi tidak tersedia.', 403);
        $signature = $this->input->post('signature');
        if (!$signature || strpos($signature, 'data:image/png;base64,') !== 0) { $this->session->set_flashdata('error', 'Tanda tangan wajib diisi.'); redirect('tickets/'.$id); }
        $directory = FCPATH.'uploads/signatures/';
        if (!is_dir($directory)) mkdir($directory, 0755, TRUE);
        $file = 'confirm-'.$id.'-'.time().'.png';
        file_put_contents($directory.$file, base64_decode(substr($signature, 22)));
        $this->db->insert('ticket_signatures', array('ticket_id' => $id, 'tipe' => 'konfirmasi_selesai', 'file_path' => 'uploads/signatures/'.$file, 'waktu' => date('Y-m-d H:i:s')));
        $this->Ticket_model->change_status($ticket, 6, $user->id);
        $this->session->set_flashdata('success', 'Tiket telah dikonfirmasi dan ditutup.');
        redirect('tickets/'.$id);
    }
}
