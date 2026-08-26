<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function authenticate($email, $password)
    {
        $user = $this->db->select('users.*, roles.nama_role, units.nama_unit')->from('users')->join('roles', 'roles.id = users.role_id')->join('units', 'units.id = users.unit_id', 'left')->where(array('users.email' => $email, 'users.is_active' => 1))->get()->row();
        return ($user && password_verify($password, $user->password)) ? $user : FALSE;
    }
}
