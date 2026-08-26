<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function login()
    {
        if ($this->session->userdata('user')) redirect('dashboard');

        if ($this->input->method() === 'post') {
            $user = $this->User_model->authenticate($this->input->post('email', TRUE), $this->input->post('password'));
            if ($user) {
                $this->session->set_userdata('user', (array) $user);
                redirect('dashboard');
            }
            $this->session->set_flashdata('error', 'Email atau kata sandi tidak valid.');
        }
        $this->load->view('auth/login');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }
}
