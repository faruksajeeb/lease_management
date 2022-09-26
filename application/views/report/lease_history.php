<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $this->webspice->settings()->domain_name; ?>: Journal</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />

    <?php include(APPPATH . "views/global.php"); ?>
</head>

<body>
    <div id="wrapper">
        <div id="header_container"><?php include(APPPATH . "views/header.php"); ?></div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <form action="" method="POST" target="_blank">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="panel panel-default" style="margin:30px 0px;">
                            <div class="panel-heading">
                                <h2 style="text-align: center;"><span class="glyphicon glyphicon-file"></span> Lease Hiostory Report</h2>
                            </div>
                            <div class="panel-body">
                                <?php //echo $this->session->flashdata('message_name'); 
                                ?>
                                <div class="form_label">Vendor Name *</div>
                                <div>
                                    <select name="VENDOR_ID" id="VENDOR_ID" class="input_full form-control VENDOR_ID" required>
                                        <option value="">-- Select vendor --</option>
                                        <?php foreach ($vendors as $val) : ?>
                                            <option value="<?php echo $val->ID; ?>"><?php echo $val->VENDOR_NAME; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form_label">Lease Type *</div>
                                <div>
                                    <select name="LEASE_TYPE" id="LEASE_TYPE" class="input_full form-control LEASE_TYPE" required>
                                        <option value="">-- Select vendor first --</option>
                                        <option value="receivable">Receivable</option>
                                        <option value="payable">Payable</option>                                       
                                    </select>
                                </div>
                                <div class="form_label">Lease ID*</div>
                                <div>
                                    <select name="LEASE_ID" id="LEASE_ID" class="input_full form-control" required>
                                        <option value="">-- Select vendor & lease type first --</option>
                                        <?php foreach ($leases as $val) : ?>
                                            <option value="<?php echo $val->ID; ?>"><?php echo $val->LEASE_NAME; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="panel-footer" style="text-align:right">
                                <button class="btn btn-lg btn-default" type="submit" name="bth_submit" value="view"><span class="glyphicon glyphicon-eye-open"></span> View</button>
                                <button class="btn btn-lg btn-success" type="submit" name="bth_submit" value="export"><span class="glyphicon glyphicon-file"></span> Export</button>
                                <button class="btn btn-lg btn-primary" type="submit" name="bth_submit" value="print"><span class="glyphicon glyphicon-print"></span> Print</button>
                                <button class="btn btn-lg btn-danger" type="submit" name="bth_submit" value="pdf"><span class="glyphicon glyphicon-file"></span> PDF</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="footer_container"><?php include(APPPATH . "views/footer.php"); ?></div>
    </div>
    <script>
        $(".monthpicker").MonthPicker({
            ShowIcon: false
            // Button: '<button>...</button>'
        });
        $('#LEASE_TYPE').change(function() {
            var VendorId = $('#VENDOR_ID').val();
            var leaseType = $('#LEASE_TYPE').val();
            if (VendorId && leaseType) {
                $.ajax({
                    url: '<?php echo $url_prefix; ?>Report_controller/getLeaseIdByVendor',
                    method: 'post',
                    data: {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        vendor_id: VendorId,
                        lease_type: leaseType
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Remove options 
                        // $('#LEASE_ID').find('option').not(':first').remove();
                        $('#LEASE_ID').find('option').remove();
                        // Add options
                        $('#LEASE_ID').append('<option value="">--select lease ID--</option>');
                        $.each(response, function(index, data) {
                            $('#LEASE_ID').append('<option value="' + data['ID'] + '">' + data['LEASE_NAME'] + ' (' + data['ID'] + ')' +'</option>');
                        });
                    }
                });
            }
        });
    </script>
</body>

</html>