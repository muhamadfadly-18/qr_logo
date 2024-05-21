<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    /*
    |-------------------------------------------------------------------
    | Construct
    |-------------------------------------------------------------------
    | 
    */
    function __construct()
    {
        parent::__construct();
        $this->load->library('Ciqrcode');
        $this->load->model('home_model');
    }

    /*
    |-------------------------------------------------------------------
    | Index
    |-------------------------------------------------------------------
    |
    */
	function index()
	{
        $data['title']   = 'Codeigniter 3 - QR Code';
        $data['qr_list'] = $this->home_model->fetch_datas();

        $this->load->view('frontend/header', $data);
        $this->load->view('frontend/content', $data);
        $this->load->view('frontend/footer', $data);
    }
    
    /*
    |-------------------------------------------------------------------
    | Generate QR Code
    |-------------------------------------------------------------------
    |
    | @param $data   QR Content
    |
    */
	function generate_qrcode($data)
{
    /* Load QR Code Library */
    $this->load->library('ciqrcode');
    
    /* Data */
    $hex_data   = bin2hex($data);
    $save_name  = $hex_data.'.png';

    /* QR Code File Directory Initialize */
    $dir = 'assets/media/qrcode/';
    if (!file_exists($dir)) {
        mkdir($dir, 0775, true);
    }

    /* QR Configuration  */
    $config['cacheable']    = true;
    $config['imagedir']     = $dir;
    $config['quality']      = true;
    $config['size']         = '1024';
    $config['black']        = array(0, 0, 0);
    $config['white']        = array(255, 255, 255);
    $this->ciqrcode->initialize($config);

    /* QR Data  */
    $params['data']     = $data;
    $params['level']    = 'L';
    $params['size']     = 10;
    $params['savename'] = FCPATH.$config['imagedir']. $save_name;
    
    $this->ciqrcode->generate($params);

    /* Add Image to QR Code */
    $qr_image = imagecreatefrompng($params['savename']);
    $logo = imagecreatefrompng(FCPATH . 'assets/images/qr1.png'); // Ganti dengan path logo Anda
    $logo_width = imagesx($logo);
    $logo_height = imagesy($logo);
    $qr_width = imagesx($qr_image);
    $qr_height = imagesy($qr_image);
    $logo_x = ($qr_width - $logo_width) / 2;
    $logo_y = ($qr_height - $logo_height) / 2;
    // Menggabungkan logo ke dalam QR code
    imagecopymerge($qr_image, $logo, $logo_x, $logo_y, 0, 0, $logo_width, $logo_height, 100);
    imagepng($qr_image, $params['savename']);

    /* Return Data */
    $return = array(
        'content' => $data,
        'file'    => $dir. $save_name
    );
    return $return;
}

    
    /*
    |-------------------------------------------------------------------
    | Add Data
    |-------------------------------------------------------------------
    |
    */
	function add_data()
	{
        /* Generate QR Code */
        $data = $this->input->post('content');
        $qr   = $this->generate_qrcode($data);

        /* Add Data */
        if($this->home_model->insert_data($qr)) {
            $this->modal_feedback('success', 'Success', 'Add Data Success', 'OK');
        } else {
            $this->modal_feedback('error', 'Error', 'Add Data Failed', 'Try again');
        }
        redirect('/');

    }

    /*
    |-------------------------------------------------------------------
    | Edit Data
    |-------------------------------------------------------------------
    |
    | @param $id    ID Data
    |
    */
	function edit_data($id)
	{
        /* Old QR Data */
        $old_data = $this->home_model->fetch_data($id);
        $old_file = $old_data['file'];

        /* Generate New QR Code */
        $data = $this->input->post('content');
        $qr   = $this->generate_qrcode($data);

        /* Edit Data */
        if($this->home_model->update_data($id, $old_file, $qr)) {
            $this->modal_feedback('success', 'Success', 'Edit Data Success', 'OK');
        } else {
            $this->modal_feedback('error', 'Error', 'Edit Data Failed', 'Try again');
        }
        redirect('/');
    }

    /*
    |-------------------------------------------------------------------
    | Remove Data
    |-------------------------------------------------------------------
    |
    | @param $id    ID Data
    |
    */
	function remove_data($id)
	{
        /* Current QR Data */
        $qr_data = $this->home_model->fetch_data($id);
        $qr_file = $qr_data['file'];

        /* Delete Data */
        if($this->home_model->delete_data($id, $qr_file)) {
            $this->modal_feedback('success', 'Success', 'Delete Data Success', 'OK');
        } else {
            $this->modal_feedback('error', 'Error', 'Delete Data Failed', 'Try again');
        }
        redirect('/');
	}
    
}
