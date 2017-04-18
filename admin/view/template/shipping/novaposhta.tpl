<?php echo $header; ?>

<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/shipping.png" alt="" /> <?php echo $heading_title; ?></h1>   
            <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td>
                            <span class="required">*</span> <?php echo $entry_key; ?>
                        </td>
                        <td><input type="text" name="novaposhta_key" value="<?php echo $novaposhta_key; ?>" />
                            <?php if (isset($error_key)) { ?>
                            <span class="error"><?=$error_key;?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $entry_city_from; ?>
                        </td>
                        <td><input id="db_city" type="text" name="novaposhta_city_from" value="<?php echo $novaposhta_city_from; ?>" /></td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $type_counterpart; ?>
                        </td>
                        <td>
                            <select name="type_shipping_counterpart">
                                <option value="Organization"><?=$type_counterpart_1;?></option>
                                <option selected="selected" value="PrivatePerson"><?=$type_counterpart_2;?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $entry_sender_address; ?>
                        </td>
                        <td>
                            <select id="otdel"  name="novaposhta_sender_address" class="large-field">
                                <?php foreach ($otel_adress as $k=>$v) { ?>
                                <?php if ($k == $novaposhta_sender_address ){ ?>
                                <option selected="selected" value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $entry_sender_contact; ?>
                        </td>
                        <td><input type="text" name="novaposhta_sender_contact" value="<?php echo $novaposhta_sender_contact; ?>" /></td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $entry_sender_phone; ?>
                            <?php if (isset($error_sender_phone)) { ?>
                            <span class="error"><?=$error_sender_phone;?></span>
                            <?php } ?>
                        </td>
                        <td><input type="text" name="novaposhta_sender_phone" value="<?php echo $novaposhta_sender_phone; ?>" /></td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $entry_type_shipping; ?>
                        </td>
                        <td><select name="novaposhta_type">
                                <?php if(!isset($novaposhta_type) || ($novaposhta_type != 2 && $novaposhta_type != 3 && $novaposhta_type != 4)){
                                    $novaposhta_type = 1;
                                }?>
                                <option <?php if($novaposhta_type == 1)echo 'selected="selected"'; ?> value="1"><?=$entry_type_1;?></option>
                                <option <?php if($novaposhta_type == 2)echo 'selected="selected"'; ?> value="2"><?=$entry_type_2;?></option>
                                <option <?php if($novaposhta_type == 3)echo 'selected="selected"'; ?> value="3"><?=$entry_type_3;?></option>
                                <option <?php if($novaposhta_type == 4)echo 'selected="selected"'; ?> value="4"><?=$entry_type_4;?></option>
                            </select></td>
                    </tr>

                    <tr>
                        <td>
                            <?php echo $entry_mass; ?>
                        </td>
                        <td><input type="text" name="novaposhta_mass" size="5" value="<?php echo !empty($novaposhta_mass) ? $novaposhta_mass: '0' ?>" />
                            <?php if (isset($error_mass)) { ?>
                            <span class="error"><?=$error_mass;?></span>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php echo $entry_volume_general; ?>
                        </td>
                        <td><input type="text" name="novaposhta_volume_general" size="5" value="<?php echo !empty($novaposhta_volume_general) ? $novaposhta_volume_general: '0' ?>" />
                            <?php if (isset($error_volume_general)) { ?>
                            <span class="error"><?=$error_volume_general;?></span>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php echo $entry_publicPrice; ?>
                        </td>
                        <td><input type="text" name="novaposhta_publicPrice" size="5" value="<?php echo !empty($novaposhta_publicPrice) ? $novaposhta_publicPrice: '0' ?>" />
                            <?php if (isset($error_public_price)) { ?>
                            <span class="error"><?=$error_public_price;?></span>
                            <?php } ?>
                        </td>
                    </tr>


                    <tr>
                        <td><?php echo $entry_status; ?></td>
                        <td><select name="novaposhta_status">
                                <?php if ($novaposhta_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>

                    <tr>
                        <td><?php echo $getcity; ?></td>
                        <td><input type="submit" name="refresh" value="Обновить"></td>
                    </tr>

                </table>
            </form>
        </div>
    </div>
    <?php echo $footer; ?>

    <script type="text/javascript">
        $(function() {
        $('#db_city').autocomplete({
        source: "/index.php?route=module/novapochta/ajax_city",
        minLength: 2,
        select: function(event, ui) {
        $.post("/index.php?route=module/novapochta/ajax_otdel", {"city_id": ui.item.id}, function(data) {
        var obj = jQuery.parseJSON(data);
        $('#otdel').html('');
        for (var i in obj) {
        $('#otdel').append('<option value="' + obj[i].number + '">' + obj[i].address + '</option>');
    }
});
}
});
})
    </script>