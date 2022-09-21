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
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" >
                        <div class="panel panel-default" style="margin:30px 0px;">
                            <div class="panel-heading">
                                <h2 style="text-align: center;"><span class="glyphicon glyphicon-file"></span> Receivable Summary</h2>
                            </div>
                            <div class="panel-body">
                            <?php //echo $this->session->flashdata('message_name'); ?>
                                <div class="form_label">Vendor Name *</div>
                                <div>
                                    <select name="vendor_id" id="vendor_id" class="input_full form-control vendor_id" required>
                                        <option value="">-- Select One --</option>
                                        <option value="all">All</option>
                                        <?php foreach ($vendors as $val) : ?>
                                            <option value="<?php echo $val->ID; ?>"><?php echo $val->VENDOR_NAME; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form_label">From*</div>
                                <div class="input-group">
                                    <span class="input-group-addon glyphicon glyphicon-calendar"> Month/Year</span>
                                    <input type="text" class="form-control monthpicker" name="date_from" aria-label="" required>
                                </div>

                                <div class="form_label">To*</div>
                                <div class="input-group">
                                    <span class="input-group-addon glyphicon glyphicon-calendar"> Month/Year</span>
                                    <input type="text" class="form-control monthpicker" name="date_to" aria-label="" required>
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
    </script>
</body>

</html>