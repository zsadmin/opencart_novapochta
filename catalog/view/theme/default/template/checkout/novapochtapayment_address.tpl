<script>
    $(function() {
    
    $( "#db_city" ).autocomplete({
    source: "/index.php?route=module/novapochta/ajax_city",
    minLength: 2,
    select: function( event, ui ) {
    $('#otdel1').css({'display':'block'});
    
    $.post("/index.php?route=module/novapochta/ajax_otdel", { "city_id" : ui.item.id }, function(data){
       
var obj = jQuery.parseJSON(data);
                
$('#otdel').html('');
for(var i in obj) {
$('#otdel').append('<option>' + obj[i].address + '</option>');
                    
}
        
        
            
});
      
}
});
});
</script>



<?php if ($addresses) { ?>
<input type="radio" name="payment_address" value="existing" id="payment-address-existing" checked="checked" />
<label for="payment-address-existing"><?php echo $text_address_existing; ?></label>
<div id="payment-existing">
  <select name="address_id" style="width: 100%; margin-bottom: 15px;" size="5">
    <?php foreach ($addresses as $address) { ?>
    <?php if ($address['address_id'] == $address_id) { ?>
    <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['city']; ?>, <?php echo $address['address_2']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['city']; ?>, <?php echo $address['address_2']; ?></option>
    <?php } ?>
    <?php } ?>
  </select>
</div>

<p>
  <input type="radio" name="payment_address" value="new" id="payment-address-new" />
  <label for="payment-address-new"><?php echo $text_address_new; ?></label>
</p>

<?php } ?>
<div id="payment-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
  <table class="form">
      
      <tr style="display: none;">
      <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
      <td><input type="text" name="firstname" value="<?php echo $address['firstname'] ?>" class="large-field" /></td>
    </tr>
    <tr style="display: none;">
      <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
      <td><input type="text" name="lastname" value="<?php echo $address['lastname'] ?>" class="large-field" /></td>
    </tr>
    <tr style="display: none;">
      <td><?php echo $entry_company; ?></td>
      <td><input type="text" name="company" value="<?php echo $address['company'] ?>" class="large-field" /></td>
    </tr>
    
    <?php if ($company_id_display) { ?>
    <tr style="display: none;">
      <td><?php if ($company_id_required) { ?>
        <span class="required">*</span>
        <?php } ?>
        <?php echo $entry_company_id; ?></td>
      <td><input type="text" name="company_id" value="<?php echo $address['company_id'] ?>" class="large-field" /></td>
    </tr>
    <?php } ?>
    <?php if ($tax_id_display) { ?>
    <tr style="display: none;">
      <td><?php if ($tax_id_required) { ?>
        <span class="required">*</span>
        <?php } ?>
        <?php echo $entry_tax_id; ?></td>
      <td><input type="text" name="tax_id" value="<?php echo $address['tax_id'] ?>" class="large-field" /></td>
    </tr>
    <?php } ?>
    <tr style="display: none;">
      <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
      <td><input type="text" name="address_1" value="<?php echo $address['address_1'] ?>" class="large-field" /></td>
    </tr>
   
    <tr>
      <td><span class="required">*</span> <?php echo $entry_city; ?> <br /> <input id="db_city" type="text" name="city" value="" class="large-field" /></td>
    </tr>
    
    <tr>
       <td> <div id="otdel1" style="display: none;">
        <br />
        <span class="required">*</span> Отделение Новая Почта:<br />
        <select id ="otdel"  name="address_2" value="<?php echo $address_2; ?>" class="large-field"></select>
    </div></td>
    </tr>
    
    
    <tr style="display: none;">
      <td><span id="payment-postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
      <td><input type="text" name="postcode" value="" class="large-field" /></td>
    </tr>
    <tr style="display: none;">
      <td><span class="required">*</span> <?php echo $entry_country; ?></td>
      <td><select name="country_id" class="large-field">
              <option value="Ukraine">Ukraine</option>
          
        </select></td>
    </tr>
    <tr style="display: none;">
      <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
      <td><select name="zone_id" class="large-field">
              <option value="Kiev">Kiev</option>   
        </select></td>
    </tr>
  </table>
</div>
<br />
<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_continue; ?>" id="button-payment-address" class="button" />
  </div>
</div>
<script type="text/javascript"><!--
$('#payment-address input[name=\'payment_address\']').live('change', function() {
	if (this.value == 'new') {
		$('#payment-existing').hide();
		$('#payment-new').show();
	} else {
		$('#payment-existing').show();
		$('#payment-new').hide();
	}
});
//--></script> 
<script type="text/javascript"><!--
$('#payment-address select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('#payment-address select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#payment-postcode-required').show();
			} else {
				$('#payment-postcode-required').hide();
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (!$.isEmptyObject(json['zone'])) {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('#payment-address select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#payment-address select[name=\'country_id\']').trigger('change');
//--></script>
