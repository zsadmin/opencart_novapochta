<?php

class ControllerModuleNovapochta extends Controller {

    public function ajax_city() {

        // Chech input
        if (empty($_GET['term'])) {
            die();
        }

        $this->load->model('module/novapochta');

        $data = $this->model_module_novapochta->getCityByName($_GET['term']);

        die(json_encode($data));
    }

    public function ajax_otdel() {
        $this->load->model('module/novapochta');

        $data = $this->model_module_novapochta->getOtdelById($_POST["city_id"]);

        die(json_encode($data));
    }

    public function ajax_create_shipping() {
        if (!empty($_POST)) {
            $this->load->model('module/novapochta');
            $data = $this->model_module_novapochta->create_shipping($_POST);
            die($data);
        } else {
            die(FALSE);
        }
    }

    public function ajax_cancel_shipping() {
        if (!empty($_POST['np_id'])) {
            $this->load->model('module/novapochta');
            $data = $this->model_module_novapochta->cancel_shipping($this->db->escape($_POST['np_id']));
            die($data);
        } else {
            die(FALSE);
        }
    }

}

?>
