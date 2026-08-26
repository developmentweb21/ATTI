<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_lib {
    protected $CI;
    public function __construct() { $this->CI =& get_instance(); $this->CI->load->config('email', TRUE); $this->CI->load->library('email', $this->CI->config->item('email')); }
    public function ticket_created($ticket_id) {
        $ticket = $this->CI->Ticket_model->find($ticket_id);
        $technicians = $this->CI->db->select('email,nama')->from('users u')->join('roles r','r.id=u.role_id')->where(array('r.nama_role'=>'Teknisi','u.is_active'=>1))->get()->result();
        foreach ($technicians as $tech) $this->send($ticket, $tech->email, $tech->nama, 'Tiket IT baru: '.$ticket->no_tiket, 'Ada tiket baru yang perlu ditangani.');
    }
    public function status_changed($ticket_id, $status) {
        $ticket = $this->CI->Ticket_model->find($ticket_id);
        if (!$ticket || !$ticket->user_id) return;
        $user = $this->CI->db->get_where('users', array('id'=>$ticket->user_id,'is_active'=>1))->row();
        if ($user) $this->send($ticket, $user->email, $user->nama, 'Pembaruan tiket: '.$ticket->no_tiket, 'Status tiket Anda berubah menjadi <strong>'.html_escape($status).'</strong>.');
    }
    protected function send($ticket, $email, $name, $subject, $intro) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return;
        $html = '<div style="font-family:Arial,sans-serif;color:#17202d;max-width:600px"><h2 style="color:#246bfd">PDE Service Desk</h2><p>Halo '.html_escape($name).',</p><p>'.$intro.'</p><table style="border-collapse:collapse;width:100%"><tr><td style="padding:8px;border:1px solid #e5e7eb">No. tiket</td><td style="padding:8px;border:1px solid #e5e7eb"><b>'.html_escape($ticket->no_tiket).'</b></td></tr><tr><td style="padding:8px;border:1px solid #e5e7eb">Pelapor</td><td style="padding:8px;border:1px solid #e5e7eb">'.html_escape($ticket->nama_pelapor).'</td></tr><tr><td style="padding:8px;border:1px solid #e5e7eb">Keluhan</td><td style="padding:8px;border:1px solid #e5e7eb">'.nl2br(html_escape($ticket->deskripsi_aduan)).'</td></tr></table><p style="color:#6b7280">Email ini dikirim otomatis oleh PDE Service Desk.</p></div>';
        $this->CI->email->clear(TRUE); $this->CI->email->from($this->CI->config->item('smtp_user', 'email'), 'PDE Service Desk'); $this->CI->email->to($email); $this->CI->email->subject($subject); $this->CI->email->message($html); $sent=$this->CI->email->send();
        $this->CI->db->insert('notification_logs', array('ticket_id'=>$ticket->id,'tipe'=>'email','tujuan'=>$email,'status_kirim'=>$sent?'terkirim':'gagal','waktu_kirim'=>date('Y-m-d H:i:s')));
    }
}
