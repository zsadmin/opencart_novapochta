<?php

class ModelShippingnovaposhta extends Model {

    function getNovaposhtaType($type_num) {
        switch($type_num){
            case 1:
                $novaposhta_type = 'DoorsDoors';
                break;
            case 2:
                $novaposhta_type = 'DoorsWarehouse';
                break;
            case 3:
                $novaposhta_type = 'WarehouseDoors';
                break;
	        case 4:
		        $novaposhta_type = 'WarehouseDoors';
		        break;
            default:
                $novaposhta_type = '';
                break;
        }
        return $novaposhta_type;
    }

    function getQuote() {
        require_once __DIR__ . './../module/novapochta_api2.php';

        $api = new NovaPoshtaApi2($this->config->get('novaposhta_key'));

        $this->load->language('shipping/novaposhta');
        $this->load->model('account/address');

        $citySender = $this->config->get('novaposhta_city_from');

        if ($this->customer->isLogged()) {
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
            $cityRecipient    = $shipping_address['city'];
        } else {
            $cityRecipient = $this->session->data['guest']['shipping']['city'];
        }

        $recCity         = $api->getCity($cityRecipient, $cityRecipient);
        $senCity         = $api->getCity($citySender, $citySender);
        $novaposhta_type = $this->getNovaposhtaType(intval($this->config->get('novaposhta_type')));

        if (isset($recCity['data'][0]['Ref'], $senCity['data'][0]['Ref']) && ! empty($novaposhta_type)) {
            $price = $api->getDocumentPrice(
                $senCity['data'][0]['Ref'],
                $recCity['data'][0]['Ref'],
                $novaposhta_type,
                $this->cart->getWeight(),
                $this->cart->getTotal()
            );

            $deliveryDate = $api->getDocumentDeliveryDate(
                $senCity['data'][0]['Ref'],
                $recCity['data'][0]['Ref'],
                $novaposhta_type,
                date('d.m.Y')
            );

            if ($price["success"] == true && isset($price["data"][0])
                && $deliveryDate["success"] == true && isset($deliveryDate["data"][0])
            ) {
                $cost = intval($price["data"][0]["Cost"]);
                $text = sprintf(
                    $this->language->get('text_shipping'),
                    date('d.m.Y', strtotime($deliveryDate["data"][0]["DeliveryDate"]["date"])),
                    $cost
                );
            } else {
                $error = sprintf(
                    $this->language->get('error_api'),
                    implode(', ', $price["errors"]) . ', ' . implode(', ', $deliveryDate["errors"])
                );
            }

            $quote_data['novaposhta'] = array(
                'code'         => 'novaposhta.novaposhta',
                'title'        => $this->language->get('text_description'),
                'cost'         => ! empty($cost) ? $cost : 0,
                'tax_class_id' => 0,
                'text'         => ! empty($text) ? $text : ''
            );
            $method_data              = array(
                'code'       => 'novaposhta',
                'title'      => $this->language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('novaposhta_sort_order'),
                'error'      => ! empty($error) ? $error : false
            );

            return $method_data;
        }
    }

}
