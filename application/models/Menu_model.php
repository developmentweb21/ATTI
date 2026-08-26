<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    public function for_role($role_id)
    {
        return $this->db->select('m.*')->from('menus m')->join('role_menus rm', 'rm.menu_id = m.id')->where(array('rm.role_id' => $role_id, 'm.is_active' => 1))->order_by('m.urutan')->get()->result();
    }

    public function all()
    {
        return $this->db->order_by('urutan')->get('menus')->result();
    }

    public function allowed($role_id, $path)
    {
        return $this->db->from('menus m')->join('role_menus rm', 'rm.menu_id = m.id')->where(array('rm.role_id' => $role_id, 'm.is_active' => 1))->where('m.url', $path)->count_all_results() > 0;
    }
}
