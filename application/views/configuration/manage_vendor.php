<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $this->webspice->settings()->domain_name; ?>: Vendor</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />

    <?php include(APPPATH."views/global.php"); ?>
</head>

<body>
    <div id="wrapper">
        <div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>

        <div id="page_manage_vendor" class="container-fluid page_identifier">
            <div class="page_caption">Manage Vendor</div>
            <div class="page_body table-responsive">
                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <!--filter section-->
                        <form id="frm_filter" method="get" action="" data-parsley-validate>
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                value="<?php echo $this->security->get_csrf_hash(); ?>">

                            <table style="width:auto;">
                                <tr>
                                    <td>Keyword</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" name="SearchKeyword" class="input_style input_full" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10">
                                        <input type="submit" name="filter" class="btn btn-info" value="Filter Data" />
                                        <a class="btn btn-default"
                                            href="<?php echo $url_prefix; ?>manage_vendor">Refresh</a>
                                        <a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_vendor/print"
                                            target="_blank">Print</a>
                                        <a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_vendor/csv"
                                            target="_blank">Export</a>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <div class="user_tips">
                            <div class="title">Tips to All Users.</div>
                            <div class="description">
                                <ul>
                                    <li>Filter by any value in <b>Keyword</b> field.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> <!-- end of the row -->
                <br />
                <?php if( !isset($filter_by) || !$filter_by ){$filter_by = 'All Data';} ?>
                <div class="breadcrumb">Filter By: <?php echo $filter_by; ?></div>

                <div id="data_table" style="overflow:auto;">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Sl.</th>
                            <th>Vendor Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>E-mail</th>
                            <th>Contact person</th>
                            <th>Contact person number</th>
                            <th>VAT Reg. no.</th>
                            <th>Trade Licence No.</th>
                            <th>TIN no.</th>
                            <th>Business Experience</th>
                            <th>Corporate Status </th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php foreach($get_record as $k=>$v): ?>
                        <tr>
                            <td><?php echo ++$k ?></td>
                            <td><?php echo $v->VENDOR_NAME; ?></td>
                            <td><?php echo $v->ADDRESS; ?></td>
                            <td><?php echo $v->PHONE; ?></td>
                            <td><?php echo $v->EMAIL; ?></td>
                            <td><?php echo $v->CONTACT_PERSON; ?></td>
                            <td><?php echo $v->CONTACT_PERSON_NUMBER ?></td>
                            <td><?php echo $v->VAT_REG_NO ?></td>
                            <td><?php echo $v->TRADE_LICENCE_NO ?></td>
                            <td><?php echo $v->TIN_NO ?></td>
                            <td><?php echo $v->BUSINESS_EXPERIENCE ?></td>
                            <td><?php echo $v->CORPORATE_STATUS ?></td>
                            <td><?php echo $this->webspice->static_status($v->STATUS); ?></td>
                            <td>
                                <?php if( $this->webspice->permission_verify('manage_vendor',true) ): ?>
                                <a href="<?php echo $url_prefix; ?>manage_vendor/edit/<?php echo $this->webspice->encrypt_decrypt($v->ID,'encrypt'); ?>"
                                    class="btn btn-xs btn-primary" data-featherlight="ajax">Edit</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_vendor',true) && $v->STATUS==7 ): ?>
                                <a href="<?php echo $url_prefix; ?>manage_vendor/inactive/<?php echo $this->webspice->encrypt_decrypt($v->ID,'encrypt'); ?>"
                                    class="btn btn-xs btn-warning btn_ajax">Inactive</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_vendor',true) && $v->STATUS==-7 ): ?>
                                <a href="<?php echo $url_prefix; ?>manage_vendor/active/<?php echo $this->webspice->encrypt_decrypt($v->ID,'encrypt'); ?>"
                                    class="btn btn-xs btn-success btn_ajax">Active</a>
                                <?php endif; ?>
                                <div class="spinner">&nbsp;</div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <div id="pagination"><?php echo $pager; ?><div class="float_clear_full">&nbsp;</div>
                </div>

            </div>
            <!--end .page_body-->

        </div>

        <div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
    </div>
</body>

</html>