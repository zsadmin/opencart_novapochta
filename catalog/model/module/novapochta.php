<?php

class ModelModuleNovapochta extends Model {

    public function getCityByName($name = '') {
        // Check data

        if (empty($name)) {

            return FALSE;
        }

        $name = $this->db->escape($name);

        // Prepare data

        $name = htmlspecialchars($name);

        $query = $this->db->query("SELECT city_id as id, nameRu as label, nameRu as value FROM " . DB_PREFIX . "novapochta_city WHERE nameRu LIKE '%" . $name . "%'");

        return $query->rows;
    }

    public function getOtdelById($id = '') {

        $id = intval($id);

        $query = $this->db->query("SELECT number, address FROM " . DB_PREFIX . "novapochta_address WHERE city_id = " . $id . "");

        return $query->rows;
    }

    public function cancel_shipping($np_id = 0)
    {
        require_once 'novapochta_api2.php';

        $api = new NovaPoshtaApi2($this->config->get('novaposhta_key'));
        $result = $this->db->query("SELECT `np_ref` FROM " . DB_PREFIX . "novapochta_order WHERE np_id = '" . $np_id . "'");
        $ref = $result->row['np_ref'];

        if (isset($ref)) {
            $res = $api->deleteInternetDocument($ref);
            if ($res["success"] == true) {
                $result = $this->db->query("SELECT `order_id` FROM " . DB_PREFIX . "novapochta_order WHERE np_id = '" . $np_id . "'");
                $order_id = $result->row['order_id'];
                $this->db->query("DELETE FROM " . DB_PREFIX . "novapochta_order WHERE np_id = '" . $np_id . "'");
                $str = "UPDATE `" . DB_PREFIX . "order` SET `shipping_method`='Доставка Новой Почтой' WHERE `order_id`=" . $order_id . ";";
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `shipping_method`='Доставка Новой Почтой' WHERE `order_id`=" . $order_id . ";");
                return 'DELETED';
            } else {
                return isset($res["errors"][0]) ? $res["errors"][0] : false;
            }
        }
        return false;
    }

    public function create_shipping($data = array())
    {
        require_once 'novapochta_api2.php';
        //validating
        $error_validating = $this->validate_shipping($data);
        if ($error_validating) {
            $this->load->language('shipping/novaposhta');
            return json_encode(array('error' => $this->language->get($error_validating)));
        }

        //prepare data
        $order_id = $this->db->escape($data['order_id']);
        $recipient_warehouse = $this->db->query("SELECT `address` FROM " . DB_PREFIX . "novapochta_address WHERE `number` = " . intval($this->db->escape($data['novaposhta_shipping_address_2'])) . "");
        $sender_warehouse = $this->db->query("SELECT `address` FROM " . DB_PREFIX . "novapochta_address WHERE `number` = " . intval($this->db->escape($data['novaposhta_sender_address'])) . "");

        $sender = array(
            'Description' => $this->db->escape($data['novaposhta_sender_contact']),
            'City' => $this->db->escape($data['novaposhta_city_from']),
            'Region' => $this->db->escape($data['novaposhta_city_from']),
            'Warehouse' => $sender_warehouse->row['address']
        );

        $recipient = array(
            'FirstName' => trim($this->db->escape($data['novaposhta_shipping_firstname'])),
            'LastName' => trim($this->db->escape($data['novaposhta_shipping_firstname'])),
            'Phone' => $this->db->escape($data['novaposhta_telephone']),
            'City' => $this->db->escape($data['novaposhta_shipping_city']),
            'Region' => $this->db->escape($data['novaposhta_shipping_city']),
            'Warehouse' => $recipient_warehouse->row['address'],
            'CounterpartyType' => $this->db->escape($data['type_shipping_counterpart'])
        );
        $params = array(
            'Description' => $this->db->escape($data['novaposhta_description']),
            'Weight' => $this->db->escape($data['novaposhta_mass']),
            'Cost' => $this->db->escape($data['novaposhta_publicPrice']),
            'ServiceType' => $this->db->escape($data['novaposhta_type']),
            'CargoType' => $this->db->escape($data['novaposhta_cargo_type']),
            'VolumeGeneral' => $this->db->escape($data['novaposhta_volume_general']),
            'PaymentMethod' => $this->db->escape($data['novaposhta_pay_type']),
            'PayerType' => $this->db->escape($data['novaposhta_payer']),
            'DateTime' => $this->db->escape($data['novaposhta_need_date']),
            'SeatsAmount' => '1'
        );

        $api = new NovaPoshtaApi2($this->config->get('novaposhta_key'));
        $res = $api->newInternetDocument($sender, $recipient, $params);

        if ($res["success"] == true) {
            $np_id = isset($res["data"][0]["IntDocNumber"]) ? (int)$res["data"][0]["IntDocNumber"] : '';
            $np_ref = isset($res["data"][0]["Ref"]) ? $res["data"][0]["Ref"] : '';
            $price = isset($res["data"][0]["CostOnSite"]) ? (int)$res["data"][0]["CostOnSite"] : '';

            if (isset($np_id) && isset($np_ref) && isset($price)) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "novapochta_order` (`id_novapochta_order`, `order_id`, `np_id`, `np_ref`, `price`) VALUES ('', '" . "$order_id" . "', '" . "$np_id" . "', '" . "$np_ref" . "', '" . "$price" . "');");
                $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET `text`='" . $this->currency->format(round($price), $this->config->get('config_currency')) . "', `value` =" . $price . " WHERE `order_id`=" . $order_id . " AND `code`='shipping';");
                $this->load->model('account/order');
                $total = $this->model_account_order->getOrderTotals($order_id);

	            //if shipping cost change calculate new total
                $new_total = 0;
                foreach($total as $item){
                    if($item['code'] == 'total'){
                        continue;
                    }
                    $new_total += floatval($item['value']);
                }
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `shipping_method`='Доставка Новой Почтой ( " . $np_id . " )', `total`=" . $new_total . " WHERE `order_id`=" . $order_id . ";");
                $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET `text`='" . $this->currency->format(round($new_total, 2), $this->config->get('config_currency')) . "', `value` =" . $new_total. " WHERE `order_id`=" . $order_id . " AND `code`='total';");

	            return json_encode(array('success' => $np_id));
            }
        }

	    return json_encode(array('error' => 'API errors: ' . implode(', ', $res["errors"])));
    }

    protected function validate_shipping($data = array())
    {
	    if (empty(trim($data['novaposhta_description']))) {
		    return "error_description";
	    }
        if (!mb_ereg_match('[0-9]{1,5}(\.[0-9]{1,3})?$', $data['novaposhta_publicPrice'])) {
            return "error_public_price";
        }
        if (!mb_ereg_match('[0-9]{1,3}(\.[0-9]{1,3})?$', trim($data['novaposhta_mass']))) {
            return "error_mass";
        }
        if (!mb_ereg_match('[0-9]{1,3}(\.[0-9]{1,5})?$', trim($data['novaposhta_volume_general']))) {
            return "error_volume_general";
        }
        return false;
    }
}
