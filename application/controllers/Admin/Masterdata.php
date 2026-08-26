<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Masterdata extends CI_Controller {
    private $user;
    private $masters = array(
        'units'=>array('title'=>'Unit kerja','table'=>'units','fields'=>array('nama_unit'=>'Nama unit')),
        'types'=>array('title'=>'Jenis keluhan','table'=>'jenis_keluhan','fields'=>array('nama_jenis'=>'Nama jenis')),
        'spareparts'=>array('title'=>'Sparepart','table'=>'sparepart','fields'=>array('nama_sparepart'=>'Nama sparepart','satuan'=>'Satuan','stok'=>'Stok')),
        'statuses'=>array('title'=>'Status tiket','table'=>'status_tiket','fields'=>array('nama_status'=>'Nama status','color_code'=>'Kode warna','urutan'=>'Urutan')),
        'users'=>array('title'=>'Pengguna','table'=>'users','fields'=>array('nama'=>'Nama lengkap','email'=>'Email','no_hp'=>'No. HP','role_id'=>'Role','unit_id'=>'Unit','is_active'=>'Status'))
    );
    public function __construct(){parent::__construct();if(!$this->session->userdata('user'))redirect('login');$this->user=(object)$this->session->userdata('user');if($this->user->nama_role!=='Admin')show_error('Halaman ini khusus Admin.',403);}
    public function index($section='units'){if(!isset($this->masters[$section]))show_404();$meta=$this->masters[$section];$data=array('section'=>$section,'meta'=>$meta,'masters'=>$this->masters,'rows'=>$this->records($section),'roles'=>$this->db->get('roles')->result(),'units'=>$this->db->get('units')->result());$this->load->view('layouts/header',array('title'=>'Master Data'));$this->load->view('admin/masterdata',$data);$this->load->view('layouts/footer');}
    public function save($section,$id=NULL){if(!isset($this->masters[$section])||$this->input->method()!=='post')show_404();$meta=$this->masters[$section];$data=array();foreach($meta['fields'] as $field=>$label){$data[$field]=$section==='users'&&$field==='is_active'?($this->input->post($field)?1:0):$this->input->post($field,TRUE);}if($section==='users'){$password=$this->input->post('password');if($password)$data['password']=password_hash($password,PASSWORD_DEFAULT);if(!$id&&!$password){$this->session->set_flashdata('error','Password wajib diisi untuk pengguna baru.');redirect('admin/'.$section);}}if($id)$this->db->where('id',(int)$id)->update($meta['table'],$data);else $this->db->insert($meta['table'],$data);$this->session->set_flashdata('success',$id?'Data berhasil diperbarui.':'Data baru berhasil ditambahkan.');redirect('admin/'.$section);}
    public function delete($section,$id){if(!isset($this->masters[$section])||$this->input->method()!=='post')show_404();$this->db->where('id',(int)$id)->delete($this->masters[$section]['table']);$this->session->set_flashdata($this->db->affected_rows()?'success':'error',$this->db->affected_rows()?'Data berhasil dihapus.':'Data tidak dapat dihapus karena masih dipakai atau tidak ditemukan.');redirect('admin/'.$section);}
    private function records($section){if($section==='users')return $this->db->select('users.*,roles.nama_role,units.nama_unit')->from('users')->join('roles','roles.id=users.role_id')->join('units','units.id=users.unit_id','left')->order_by('users.id','DESC')->get()->result();return $this->db->order_by('id','DESC')->get($this->masters[$section]['table'])->result();}
}
