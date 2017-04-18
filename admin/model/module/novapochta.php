<?php

class ModelModulenovapochta extends Model {

    public function createTables() {
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "novapochta_city ( city_id INT, PRIMARY KEY(city_id), city_ref varchar(128), nameRu varchar(128) COLLATE utf8_general_ci)");
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "novapochta_address ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), city_id int, number int, address_ref varchar(128), address varchar(255) COLLATE utf8_general_ci)");
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "novapochta_order ( id_novapochta_order INT AUTO_INCREMENT, PRIMARY KEY(id_novapochta_order), order_id INT, np_id varchar(255), np_ref varchar(128), price FLOAT COLLATE utf8_general_ci)");
    }

    public function getCity($key = '') {
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<file>';
        $xml .= '	<apiKey>' . $this->db->escape($key) . '</apiKey>';
        $xml .= '	<calledMethod>getCities</calledMethod>';
        $xml .= '	<methodProperties/>';
        $xml .= '   <modelName>Address</modelName>';
        $xml .= '</file>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/xml/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = new SimpleXMLElement($result);

        if ($response->success == 'true') {
            $this->clear_table_cities();
            $zapros = "";
            foreach ($response->data->item as $city) {
                $city_id = (string)$city->CityID;
                $city_ref = (string)$city->Ref;
                $nameRu = (string)(!empty($city->DescriptionRu) ? $city->DescriptionRu : $city->Description);
                $zapros .= "('" . "$city_id" . "', '" . "$city_ref" . "', '" . "$nameRu" . "'),";
            }
            $zapros = substr_replace($zapros, '', strlen($zapros) - 1, strlen($zapros));
            $this->db->query("INSERT INTO " . DB_PREFIX . "novapochta_city (`city_id`, `city_ref`, `nameRu`) VALUES  $zapros;");
        } else {
            return (string)$response->errors->item;
        }
    }

    /**
     * Clear novapochta_city table
     *
     * @return void
     */
    protected function clear_table_cities(){
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "novapochta_city;");
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "novapochta_city ( city_id INT, PRIMARY KEY(city_id), city_ref varchar(128), nameRu varchar(128) COLLATE utf8_general_ci)");
    }

    public function count_novapochta_size($order_products) {
        $V = 0;
        foreach ($order_products as $product) {
            $query = $this->db->query("SELECT product_id, length, width, height FROM " . DB_PREFIX . "product WHERE product_id=" . intval($product['product_id']));
            if ($query->row && $query->row['length'] != 0 && $query->row['width'] != 0 && $query->row['height'] != 0) {
                $V += $query->row['length'] * $query->row['width'] * $query->row['height'] * $product['quantity'];
            } else {
                $V += $this->config->get('novaposhta_depth') * $this->config->get('novaposhta_width') * $this->config->get('novaposhta_height') * $product['quantity'];
            }
        }
        return pow($V, 1 / 3);
    }

    public function count_novapochta_length_desc($order_products) {
        $where = 'WHERE ';
        foreach ($order_products as $product) {
            $where.='`product_id` =' . $product['product_id'] . ' OR ';
        }
        $where = substr_replace($where, "", -4);
        $query = $this->db->query("SELECT length FROM " . DB_PREFIX . "product " . $where);
        $res = '(';
        foreach ($query->rows as $row) {
            $res .= round($row['length'], 2);
            $res .= ', ';
        }
        $res = substr_replace($res, "", -2) . ')';
        return $res;
    }
    
    public function count_novapochta_width_desc($order_products) {
        $where = 'WHERE ';
        foreach ($order_products as $product) {
            $where.='`product_id` =' . $product['product_id'] . ' OR ';
        }
        $where = substr_replace($where, "", -4);
        $query = $this->db->query("SELECT width FROM " . DB_PREFIX . "product " . $where);
        $res = '(';
        foreach ($query->rows as $row) {
            $res .= round($row['width'], 2);
            $res .= ', ';
        }
        $res = substr_replace($res, "", -2) . ')';
        return $res;
    }
    
    public function count_novapochta_height_desc($order_products) {
        $where = 'WHERE ';
        foreach ($order_products as $product) {
            $where.='`product_id` =' . $product['product_id'] . ' OR ';
        }
        $where = substr_replace($where, "", -4);
        $query = $this->db->query("SELECT height FROM " . DB_PREFIX . "product " . $where);
        $res = '(';
        foreach ($query->rows as $row) {
            $res .= round($row['height'], 2);
            $res .= ', ';
        }
        $res = substr_replace($res, "", -2) . ')';
        return $res;
    }

    public function getAdress($key = '') {
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<file>';
        $xml .= '	<apiKey>' . $this->db->escape($key) . '</apiKey>';
        $xml .= '	<calledMethod>getWarehouses</calledMethod>';
        $xml .= '	<methodProperties/>';
        $xml .= '   <modelName>Address</modelName>';
        $xml .= '</file>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/xml/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = new SimpleXMLElement($result);

        if ($response->success == 'true') {
            $this->clear_table_address();
            $zapros = "";
            $id_and_ref = $this->get_cities_id_by_ref();
            foreach ($response->data->item as $warenhouse) {
                if (array_key_exists((string)$warenhouse->CityRef, $id_and_ref)) {
                    $cityId = $id_and_ref[(string)$warenhouse->CityRef];
                    $wareId = (string)$warenhouse->SiteKey;
                    $wareRef = (string)$warenhouse->Ref;
                    $addressRu = (string)(!empty($warenhouse->DescriptionRu) ? $warenhouse->DescriptionRu : $warenhouse->Description);
                    $zapros .= "('" . "$cityId" . "', '" . "$wareId" . "', '" . "$wareRef" . "', '" . "$addressRu" . "'),";
                }
            }

            $zapros = substr_replace($zapros, '', strlen($zapros) - 1, strlen($zapros));
            $this->db->query("INSERT INTO " . DB_PREFIX . "novapochta_address (`city_id`, `number`, `address_ref`, `address`) VALUES  $zapros;");
        } else {
            return (string)$response->errors->item;
        }
    }

    /**
     * Get cities id and ref
     *
     * @return array
     */
    protected function get_cities_id_by_ref(){
        $sql = $this->db->query("SELECT `city_id`, `city_ref` FROM `" . DB_PREFIX . "novapochta_city`");
        $id_and_ref = array();
        foreach($sql->rows as $row){
            $id_and_ref[(string)$row["city_ref"]] = (string)$row["city_id"];
        }
        return $id_and_ref;
    }

    /**
     * Clear novapochta_address table
     *
     * @return void
     */
    protected function clear_table_address(){
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "novapochta_address;");
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "novapochta_address ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), city_id int, number int, address_ref varchar(128), address varchar(255) COLLATE utf8_general_ci)");
    }

    public function get_otdel_address($name = '') {

        //prepare data
        $name = $this->db->escape($name);

        $sql = $this->db->query("SELECT `" . DB_PREFIX . "novapochta_address`.`number`, `" . DB_PREFIX . "novapochta_address`.`address` FROM `" . DB_PREFIX . "novapochta_city` LEFT JOIN `" . DB_PREFIX . "novapochta_address` ON `" . DB_PREFIX . "novapochta_city`.`city_id` = `" . DB_PREFIX . "novapochta_address`.`city_id` WHERE `" . DB_PREFIX . "novapochta_city`.`nameRu` = '" . $name . "'");


        $result = array();

        if (!empty($sql->rows)) {
            foreach ($sql->rows as $v) {
                $result[$v['number']] = $v['address'];
            }
        }
        return $result;
    }

    public function get_np_order($order_id = 0) {
        //prepare data 
        $order_id = intval($order_id);

        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "novapochta_order ( id_novapochta_order INT AUTO_INCREMENT, PRIMARY KEY(id_novapochta_order), order_id INT, np_id varchar(255) COLLATE utf8_general_ci)");
        $sql = $this->db->query("SELECT `np_id` FROM `" . DB_PREFIX . "novapochta_order` WHERE `order_id` = '" . $order_id . "'");
        $result = (!empty($sql->row['np_id'])) ? $sql->row['np_id'] : 0;

        return $result;
    }

    public function get_mass($order_id = 0) {
        //prepare data
        $order_id = intval($order_id);

        $sql = $this->db->query("SELECT `" . DB_PREFIX . "order_product`.`quantity`, `" . DB_PREFIX . "product`.`weight`, `" . DB_PREFIX . "order_product`.`product_id` FROM `" . DB_PREFIX . "order_product` LEFT JOIN `" . DB_PREFIX . "product` ON `" . DB_PREFIX . "order_product`.`product_id` = `" . DB_PREFIX . "product`.`product_id` WHERE `" . DB_PREFIX . "order_product`.`order_id` = '" . $order_id . "'");
        if (!empty($sql->rows)) {
            $mass = 0;
            foreach ($sql->rows as $v) {
                if ($v['weight'] != 0) {
                    $mass = $mass + ($v['weight'] * $v['quantity']);
                } else {
                    $mass = $mass + ($this->config->get('novaposhta_mass') * $v['quantity']);
                }
            }
        }
        return $mass;
    }

}
