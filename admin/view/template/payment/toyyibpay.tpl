<?php

echo $header; ?>
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
            <h1><img src="view/image/payment/toyyibpay-icon.png" alt="" height="22" width="22" /><?php echo $heading_title; ?></h1>
            <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
				
					<tr>
                        <td><?php echo $entry_company_email; ?></td>
                        <td><input type="text" name="toyyibpay_company_email" value="<?php echo $toyyibpay_company_email; ?>" size="40" />  
                        </td>
                    </tr>
					
					<tr>
                        <td><?php echo $entry_company_phone; ?></td>
                        <td><input type="text" name="toyyibpay_company_phone" value="<?php echo $toyyibpay_company_phone; ?>" size="15" />  
                        </td>
                    </tr>
					
					
                    <tr>
                        <td><?php echo $entry_api_key; ?></td>
                        <td><input type="text" name="toyyibpay_api_key" value="<?php echo $toyyibpay_api_key; ?>" size="40" />
                        <?php if ($error_api_key) { ?>
                            <span class="error"><?php echo $error_api_key; ?></span>
                        <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_api_environment; ?></td>
                        <td>
						<select name="toyyibpay_api_environment">
							<option value="">[ Select ] <?php /* echo $toyyibpay_api_environment; */ ?></option>
							<option value="1" <?php if($toyyibpay_api_environment=='1'){ ?> selected="selected" <?php } ?> >Production Mode</option>
							<option value="2" <?php if($toyyibpay_api_environment=='2'){ ?> selected="selected" <?php } ?> >Sandbox Mode</option>
						</select>
                        <?php if ($error_api_environment) { ?>
                            <span class="error"><?php echo $error_api_environment; ?></span>
                        <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_category_code; ?></td>
                        <td><input type="text" name="toyyibpay_category_code" value="<?php echo $toyyibpay_category_code; ?>" />
                        <?php if ($error_category_code) { ?>
                            <span class="error"><?php echo $error_category_code; ?></span>
                        <?php } ?>
                        </td>
                    </tr>
					<tr>
                        <td><?php echo $entry_payment_channel; ?></td>
                        <td>
						<select name="toyyibpay_payment_channel">
							<option value="0" <?php if($toyyibpay_payment_channel=='0'){ ?> selected="selected" <?php } ?> >FPX</option>
							<option value="1" <?php if($toyyibpay_payment_channel=='1'){ ?> selected="selected" <?php } ?> >Credit Card</option>
							<option value="2" <?php if($toyyibpay_payment_channel=='2'){ ?> selected="selected" <?php } ?> >FPX & Credit Card</option>
						</select>
                        <?php if ($error_payment_channel) { ?>
                            <span class="error"><?php echo $error_payment_channel; ?></span>
                        <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_payment_charge; ?></td>
                        <td>
						<select name="toyyibpay_payment_charge">
							<option value="0" <?php if($toyyibpay_payment_charge=='0'){ ?> selected="selected" <?php } ?> >All charge on transaction amount</option>
							<option value="1" <?php if($toyyibpay_payment_charge=='1'){ ?> selected="selected" <?php } ?> >FPX charge on customer</option>
							<option value="2" <?php if($toyyibpay_payment_charge=='2'){ ?> selected="selected" <?php } ?> >Crefit card charge on customer</option>
                            <option value="3" <?php if($toyyibpay_payment_charge=='3'){ ?> selected="selected" <?php } ?> >All charge on customer</option>
						</select>
                        <?php if ($entry_payment_charge) { ?>
                            <span class="error"><?php echo $error_payment_charge; ?></span>
                        <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_minlimit; ?></td>
                        <td><input type="text" name="toyyibpay_minlimit" value="<?php echo $toyyibpay_minlimit; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_extra_email; ?></td>
                        <td><textarea name="toyyibpay_extra_email"/ rows="4" cols="100"><?php echo $toyyibpay_extra_email; ?></textarea>
                    </tr>
                    <tr>
                        <td><?php echo $entry_order_status; ?></td>
                        <td><select name="toyyibpay_order_status_id">
                          <?php foreach ($order_statuses as $order_status) { ?>
                          <?php if ($order_status['order_status_id'] == $toyyibpay_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                          <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_success_status; ?></td>
                        <td><select name="toyyibpay_success_status_id">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $toyyibpay_success_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_failed_status; ?></td>
                        <td><select name="toyyibpay_failed_status_id">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $toyyibpay_failed_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status; ?></td>
                        <td><select name="toyyibpay_status">
                            <?php if ($toyyibpay_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_sort_order; ?></td>
                        <td><input type="text" name="toyyibpay_sort_order" value="<?php echo $toyyibpay_sort_order; ?>" size="1" /></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?>