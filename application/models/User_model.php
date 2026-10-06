<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function check_login($username, $password)
    {
        $this->db->where('USRNM', $username);
        $this->db->where('PASSWORD', $password);
        $query = $this->db->get('usrmst');

        if ($query->num_rows() == 1) {
            return $query->row_array();
        }

        return false;
    }
}
