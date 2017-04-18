<?php

class ControllerShippingNovaposhta extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('shipping/novaposhta');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('module/novapochta');


        if (!empty($this->request->post['refresh']) && !empty($this->request->post['novaposhta_key'])) {
            if (!empty($city_result = $this->model_module_novapochta->getCity($this->request->post['novaposhta_key']))) {
                $this->session->data['error'] = $city_result;
            } elseif (!empty($address_result = $this->model_module_novapochta->getAdress($this->request->post['novaposhta_key']))) {
                $this->session->data['error'] = $address_result;
            }
        };

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('novaposhta', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
        }


        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_none'] = $this->language->get('text_none');

        $this->data['entry_tax'] = $this->language->get('entry_tax');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $this->data['getcity'] = $this->language->get('getcity');
        $this->data['entry_key'] = $this->language->get('entry_key');
        $this->data['entry_city_from'] = $this->language->get('entry_city_from');
        $this->data['entry_sender_company'] = $this->language->get('entry_sender_company');
        $this->data['entry_sender_address'] = $this->language->get('entry_sender_address');
        $this->data['entry_sender_contact'] = $this->language->get('entry_sender_contact');
        $this->data['entry_sender_phone'] = $this->language->get('entry_sender_phone');
        $this->data['entry_type_shipping'] = $this->language->get('entry_type_shipping');
        $this->data['entry_mass'] = $this->language->get('entry_mass');
        $this->data['entry_volume_general'] = $this->language->get('entry_volume_general');
        $this->data['entry_publicPrice'] = $this->language->get('entry_publicPrice');

        $this->data['entry_type_1'] = $this->language->get('entry_type_1');
        $this->data['entry_type_2'] = $this->language->get('entry_type_2');
        $this->data['entry_type_3'] = $this->language->get('entry_type_3');
        $this->data['entry_type_4'] = $this->language->get('entry_type_4');
        $this->data['entry_type_load_1'] = $this->language->get('entry_type_load_1');
        $this->data['entry_type_load_4'] = $this->language->get('entry_type_load_4');
        $this->data['type_counterpart'] = $this->language->get('type_counterpart');
        $this->data['type_counterpart_1'] = $this->language->get('type_counterpart_1');
        $this->data['type_counterpart_2'] = $this->language->get('type_counterpart_2');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['tab_general'] = $this->language->get('tab_general');

        foreach ($this->error as $key => $value) {
            $this->data[$key] = $value;
        }
        if(!isset($this->data['error_warning'])){
            $this->data['error_warning'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_shipping'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('shipping/novaposhta', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('shipping/novaposhta', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['novaposhta_key'])) {
            $this->data['novaposhta_key'] = $this->request->post['novaposhta_key'];
        } else {
            $this->data['novaposhta_key'] = $this->config->get('novaposhta_key');
        }

        if (isset($this->request->post['novaposhta_city_from'])) {
            $this->data['novaposhta_city_from'] = $this->request->post['novaposhta_city_from'];
        } else {
            $this->data['novaposhta_city_from'] = $this->config->get('novaposhta_city_from');
        }

        if (isset($this->request->post['novaposhta_sender_company'])) {
            $this->data['novaposhta_sender_company'] = $this->request->post['novaposhta_sender_company'];
        } else {
            $this->data['novaposhta_sender_company'] = $this->config->get('novaposhta_sender_company');
        }

        if (isset($this->request->post['novaposhta_sender_address'])) {
            $this->data['novaposhta_sender_address'] = $this->request->post['novaposhta_sender_address'];
        } else {
            $this->data['novaposhta_sender_address'] = $this->config->get('novaposhta_sender_address');
        }

        if (isset($this->request->post['novaposhta_sender_contact'])) {
            $this->data['novaposhta_sender_contact'] = $this->request->post['novaposhta_sender_contact'];
        } else {
            $this->data['novaposhta_sender_contact'] = $this->config->get('novaposhta_sender_contact');
        }

        if (isset($this->request->post['novaposhta_sender_phone'])) {
            $this->data['novaposhta_sender_phone'] = $this->request->post['novaposhta_sender_phone'];
        } else {
            $this->data['novaposhta_sender_phone'] = $this->config->get('novaposhta_sender_phone');
        }

        if (isset($this->request->post['novaposhta_type'])) {
            $this->data['novaposhta_type'] = $this->request->post['novaposhta_type'];
        } else {
            $this->data['novaposhta_type'] = $this->config->get('novaposhta_type');
        }

        if (isset($this->request->post['novaposhta_type_load'])) {
            $this->data['novaposhta_type_load'] = $this->request->post['novaposhta_type_load'];
        } else {
            $this->data['novaposhta_type_load'] = $this->config->get('novaposhta_type_load');
        }

        if (isset($this->request->post['novaposhta_mass'])) {
            $this->data['novaposhta_mass'] = $this->request->post['novaposhta_mass'];
        } else {
            $this->data['novaposhta_mass'] = $this->config->get('novaposhta_mass');
        }

        if (isset($this->request->post['novaposhta_volume_general'])) {
            $this->data['novaposhta_volume_general'] = $this->request->post['novaposhta_volume_general'];
        } else {
            $this->data['novaposhta_volume_general'] = $this->config->get('novaposhta_volume_general');
        }

        if (isset($this->request->post['novaposhta_publicPrice'])) {
            $this->data['novaposhta_publicPrice'] = $this->request->post['novaposhta_publicPrice'];
        } else {
            $this->data['novaposhta_publicPrice'] = $this->config->get('novaposhta_publicPrice');
        }


        if (isset($this->request->post['novaposhta_geo_zone_id'])) {
            $this->data['novaposhta_geo_zone_id'] = $this->request->post['novaposhta_geo_zone_id'];
        } else {
            $this->data['novaposhta_geo_zone_id'] = $this->config->get('novaposhta_geo_zone_id');
        }

        if (isset($this->request->post['novaposhta_status'])) {
            $this->data['novaposhta_status'] = $this->request->post['novaposhta_status'];
        } else {
            $this->data['novaposhta_status'] = $this->config->get('novaposhta_status');
        }

        if (isset($this->request->post['novaposhta_sort_order'])) {
            $this->data['novaposhta_sort_order'] = $this->request->post['novaposhta_sort_order'];
        } else {
            $this->data['novaposhta_sort_order'] = $this->config->get('novaposhta_sort_order');
        }

        $this->load->model('localisation/geo_zone');

        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->data['otel_adress'] = $this->model_module_novapochta->get_otdel_address($this->data['novaposhta_city_from']);

        $this->template = 'shipping/novaposhta.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );



        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function install() {
        $this->load->model('module/novapochta');
        $this->model_module_novapochta->createTables();
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'shipping/novaposhta')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (utf8_strlen($this->request->post['novaposhta_key']) < 32) {
            $this->error['error_key'] = $this->language->get('error_key');
        }


        if (!mb_ereg_match("^(\+38)?0[0-9]{9}$", trim($this->request->post['novaposhta_sender_phone']))) {
            $this->error['error_sender_phone'] = $this->language->get('error_sender_phone');
        }

        if (!mb_ereg_match("^([0-9]{1,3}(\.[0-9]{1,3})?)?$", trim($this->request->post['novaposhta_mass']))) {
            $this->error['error_mass'] = $this->language->get('error_mass');
        }

        if (!mb_ereg_match("^([0-9]{1,3}(\.[0-9]{1,5})?)?$", trim($this->request->post['novaposhta_volume_general']))) {
            $this->error['error_volume_general'] = $this->language->get('error_volume_general');
        }

        if (!mb_ereg_match("^([0-9]{1,5}(\.[0-9]{1,3})?)?$", trim($this->request->post['novaposhta_publicPrice']))) {
            $this->error['error_public_price'] = $this->language->get('error_public_price');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
