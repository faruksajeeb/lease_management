<?php $page_title = 'Create vendor'; ?>
<?php $route = 'create_vendor'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $this->webspice->settings()->domain_name; ?>: <?php echo $page_title; ?></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />

    <?php include(APPPATH."views/global.php"); ?>
</head>

<body>
    <div id="wrapper">
        <?php if( $this->uri->segment(2) != "edit" ): ?>
        <div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
        <?php endif; ?>

        <div id="page_<?php echo str_replace(' ','_',strtolower($page_title)); ?>"
            class="container-fluid page_identifier">
            <div class="page_caption">Create Vendor</div>
            <div class="page_body stamp">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <fieldset class="divider">
                            <legend>Please enter required information</legend>
                        </fieldset>

                        <div class="stitle">* Mandatory Field</div>

                        <form id="frm_<?php echo str_replace(' ','_',strtolower($page_title)); ?>"
                            class="form_post_ajax" form-route="<?php echo $route; ?>" method="post" action=""
                            data-parsley-validate>
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="key"
                                value="<?php if( isset($edit['ID']) && $edit['ID'] ){echo $this->webspice->encrypt_decrypt($edit['ID'], 'encrypt');} ?>" />

                            <table width="100%">
                                <tr>
                                    <td>
                                        <div class="form_label">Vendor Name*</div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="vendor_name"
                                                name="VENDOR_NAME"
                                                value="<?php echo set_value('vendor_name',$edit['VENDOR_NAME']); ?>"
                                                required />
                                            <span class="fred"><?php echo form_error('vendor_name'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">Address *</div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="address"
                                                name="ADDRESS"
                                                value="<?php echo set_value('address',$edit['ADDRESS']); ?>" required />
                                            <span class="fred"><?php echo form_error('address'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">Phone *</div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="phone" name="PHONE"
                                                value="<?php echo set_value('phone',$edit['PHONE']); ?>" required />
                                            <span class="fred"><?php echo form_error('phone'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">E-mail *</div>
                                        <div>
                                            <input type="email" class="input_full form-control" id="email" name="EMAIL"
                                                value="<?php echo set_value('email',$edit['EMAIL']); ?>" required />
                                            <span class="fred"><?php echo form_error('email'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">Contact person *</div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="contact_person"
                                                name="CONTACT_PERSON"
                                                value="<?php echo set_value('contact_person',$edit['CONTACT_PERSON']); ?>"
                                                required />
                                            <span class="fred"><?php echo form_error('contact_person'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">Contact person number *</div>
                                        <div>
                                            <input type="text" class="input_full form-control"
                                                id="contact_person_number" name="CONTACT_PERSON_NUMBER"
                                                value="<?php echo set_value('contact_person_number',$edit['CONTACT_PERSON_NUMBER']); ?>"
                                                required />
                                            <span class="fred"><?php echo form_error('contact_person_number'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">VAT Reg. no. </div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="vat_reg_no"
                                                name="VAT_REG_NO"
                                                value="<?php echo set_value('vat_reg_no',$edit['VAT_REG_NO']); ?>" />
                                            <span class="fred"><?php echo form_error('vat_reg_no'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">Trade Licence No. </div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="trade_licence_no"
                                                name="TRADE_LICENCE_NO"
                                                value="<?php echo set_value('trade_licence_no',$edit['TRADE_LICENCE_NO']); ?>" />
                                            <span class="fred"><?php echo form_error('trade_licence_no'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">TIN No. </div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="tin_no" name="TIN_NO"
                                                value="<?php echo set_value('tin_no',$edit['TIN_NO']); ?>" />
                                            <span class="fred"><?php echo form_error('tin_no'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">Business experience </div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="business_experience"
                                                name="BUSINESS_EXPERIENCE"
                                                value="<?php echo set_value('business_experience',$edit['BUSINESS_EXPERIENCE']); ?>" />
                                            <span class="fred"><?php echo form_error('business_experience'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form_label">Corporate Status </div>
                                        <div>
                                            <input type="text" class="input_full form-control" id="corporate_status"
                                                name="CORPORATE_STATUS"
                                                value="<?php echo set_value('corporate_status',$edit['CORPORATE_STATUS']); ?>" />
                                            <span class="fred"><?php echo form_error('corporate_status'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><input type="submit" class="btn btn-danger" value="Submit Data" /></div>
                                        <div class="spinner">&nbsp;</div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <div id="right_part" class="col-lg-6 col-md-6 col-sm-12">

                    </div>
                </div>


            </div>

        </div>

        <?php if( $this->uri->segment(2) != "edit" ): ?>
        <div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
        <?php endif; ?>

    </div>
</body>

</html>