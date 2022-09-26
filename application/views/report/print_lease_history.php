<?php
$url_prefix = $this->webspice->settings()->site_url_prefix;
$site_url = $this->webspice->settings()->site_url;
$domain_name = $this->webspice->settings()->domain_name;
$site_title = $this->webspice->settings()->site_title;
$total_column = 6;

$report_name = $page_title;

$companyInfo = $this->db->query("SELECT * FROM tbl_organization WHERE ID=1")->row();

# don't edit the below area (csv)
if ($action_type == 'csv') {
    $file_name = strtolower(str_replace(array('_', ' '), '', $report_name)) . '_' . date('Y_m_d_h_i') . '.xls';
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . $file_name);
    header("Pragma: no-cache");
    header("Expires: 0");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $report_name; ?></title>

    <style type="text/css">
        #printArea {
            width: 100%;
            margin: 0 auto;
        }

        body,
        table {
            font-family: tahoma;
            font-size: 13px;
        }

        .data_table td {
            padding: 2px;
            /* border:1px solid #ccc; */
        }
    </style>

    <?php if ($action_type == 'print') : ?>
        <script type="text/javascript" src="<?php echo $url_prefix; ?>global/js/jquery-3.1.1.min.js"></script>

        <link rel="stylesheet" href="<?php echo $url_prefix; ?>global/css/styles.css">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo $url_prefix; ?>global/bootstrap_3_2/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $url_prefix; ?>global/bootstrap_3_2/css/bootstrap-theme.min.css">
        <script src="<?php echo $url_prefix; ?>global/bootstrap_3_2/js/bootstrap.min.js"></script>

        <!-- print plugin -->
        <script src="<?php echo $url_prefix; ?>global/js/jquery.jqprint.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#printArea').jqprint();
                setTimeout(function() {
                    window.close();
                }, 1000);
            });
        </script>
    <?php endif; ?>
</head>

<body>
    <!--<a id="print_icon" href="#">Print</a>-->

    <div id="printArea" style=" margin-top:20px">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" colspan="<?php echo $total_column; ?>">
                    <div style="font-size:24px; font-weight:bold; color:#1E1D1D; text-align:center; padding:2px;">
                        <?php echo $companyInfo->ORGANIZATION_NAME; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td align=" center" colspan="<?php echo $total_column; ?>">
                    <div style="font-size:14px; font-weight:normal; color:#1E1D1D; text-align:center; padding:2px;">
                        <!-- <h2 style=" text-align: center;"><?php //echo $companyInfo->ORGANIZATION_NAME; 
                                                                ?></h2> -->
                        <span>
                            <?php
                            echo $companyInfo->ADDRESS . "<br/>";
                            echo "Phone: " . $companyInfo->PHONE . "<br/>";
                            echo "Fax: " . $companyInfo->FAX . "<br/>";
                            echo "Email: " . $companyInfo->EMAIL . "<br/>";
                            ?>
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <table width=" 100%" border="0" cellpadding="0" cellspacing="0">
            <tr style="border-top:1px solid #ccc;">
                <td colspan="<?php echo $total_column; ?>" align="center" style="font-size:17px; font-weight:bold; color:#d04e2a; text-align:center; padding:0px;">
                    <?php echo $report_name; ?></td>
            </tr>
            <tr style="border-top:1px solid #ccc;">
                <td colspan="<?php echo $total_column; ?>" align="center" style="font-size:17px; font-weight:bold; color:#d04e2a; text-align:center; padding:0px;">
                    <?php echo ucfirst($leaseType); ?></td>
            </tr>


            <tr>
                <td style="padding:0px;">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr style="border-top:1px solid #ccc; text-align:left">
                    <td colspan="<?php echo $total_column; ?>" style="font-size:17px; font-weight:bold; color:#d04e2a; text-align:left; padding:5px;">
                        Vendor Name: <?php echo $vendorName; ?>
                    </td>
                </tr>
                <tr style="border-top:1px solid #ccc; text-align:left">
                    <td colspan="<?php echo $total_column; ?>" style="font-size:17px; font-weight:bold; color:#d04e2a; text-align:left; padding:5px;">
                        Lease ID: <?php echo $leaseId; ?>
                    </td>
                </tr>
                <tr style="border-top:1px solid #ccc; text-align:left">
                    <td colspan="<?php echo $total_column; ?>" style="font-size:17px; font-weight:bold; color:#d04e2a; text-align:left; padding:5px;">
                        Lease Name: <?php echo $leaseName; ?>
                    </td>
                </tr>
                <tr>
                    <th colspan="6">
                        <h2 style="padding:0px;">Rent Slabs</h2>
                    </th>
                </tr>
                <tr style="background-color:#E9967A">
                    <th style="text-align:center">Sl No.</th>
                    <th style="text-align:center">From </th>
                    <th style="text-align:center">To</th>
                    <th style="text-align:right">Amount</th>
                    <th style="text-align:right">Amount With Tax</th>
                    <th style="text-align:right">Amount With Vat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rentSladAmountTotal =
                    $rentSladAmountWithTaxTotal =
                    $rentSladAmountWithVatTotal = 0;
                foreach ($leaseRentSlabs as $k => $val) :
                ?>
                    <tr>
                        <td style="text-align:center"><?php echo ++$k; ?></td>
                        <td style="text-align:center"><?php echo $val->DATE_FROM; ?></td>
                        <td style="text-align:center"><?php echo $val->DATE_TO; ?></td>
                        <td style="text-align:right"><?php echo number_format($val->AMOUNT); ?></td>
                        <td style="text-align:right"><?php echo number_format($val->AMOUNT_WITH_TAX); ?></td>
                        <td style="text-align:right"><?php echo number_format($val->AMOUNT_WITH_VAT); ?></td>
                    </tr>
                <?php
                    $rentSladAmountTotal += $val->AMOUNT;
                    $rentSladAmountWithTaxTotal += $val->AMOUNT_WITH_TAX;
                    $rentSladAmountWithVatTotal += $val->AMOUNT_WITH_VAT;
                endforeach;
                ?>
            <tfoot style="background-color:#E6E6FA">
                <tr>
                    <td colspan="3" style="text-align:left; font-weight:bold">TOTAL</td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format($rentSladAmountTotal) ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format($rentSladAmountWithTaxTotal) ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format($rentSladAmountWithVatTotal) ?></td>
                </tr>
            </tfoot>
            </tbody>
        </table>
        <br>
        <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th colspan="6">
                        <h2 style="padding:0px;">Advance</h2>
                    </th>
                </tr>
                <tr style="background-color:#E9967A">
                    <th style="text-align:center">Sl No.</th>
                    <th style="text-align:center">From </th>
                    <th style="text-align:center">To</th>
                    <th style="text-align:right">Amount</th>
                    <th style="text-align:right">Amount With Tax</th>
                    <th style="text-align:right">Amount With Vat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $advanceSladAmountTotal =
                    $advanceSladAmountWithTaxTotal =
                    $advanceSladAmountWithVatTotal = 0;
                foreach ($advanceSlabs as $k => $val) :
                ?>
                    <tr>
                        <td style="text-align:center"><?php echo ++$k; ?></td>
                        <td style="text-align:center"><?php echo $val->DATE_FROM; ?></td>
                        <td style="text-align:center"><?php echo $val->DATE_TO; ?></td>
                        <td style="text-align:right"><?php echo number_format($val->AMOUNT); ?></td>
                        <td style="text-align:right"><?php echo number_format($val->AMOUNT_WITH_TAX); ?></td>
                        <td style="text-align:right"><?php echo number_format($val->AMOUNT_WITH_VAT); ?></td>
                    </tr>
                <?php
                    $advanceSladAmountTotal += $val->AMOUNT;
                    $advanceSladAmountWithTaxTotal += $val->AMOUNT_WITH_TAX;
                    $advanceSladAmountWithVatTotal += $val->AMOUNT_WITH_VAT;
                endforeach;
                ?>
            <tfoot style="background-color:#E6E6FA">
                <tr>
                    <td colspan="3" style="text-align:left; font-weight:bold">TOTAL</td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format($advanceSladAmountTotal) ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format($advanceSladAmountWithTaxTotal) ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format($advanceSladAmountWithVatTotal) ?></td>
                </tr>
            </tfoot>
            </tbody>
        </table>
        <br>
        <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th colspan="7">
                        <h2 style="padding:0px;">Payments</h2>
                    </th>
                </tr>
                <tr style="background-color:#E9967A">
                    <th style="text-align:center">Sl No.</th>
                    <?php if ($leaseType == 'payable') : ?>
                        <th style="text-align:center">Payment Date </th>
                        <th style="text-align:center">Payment Method</th>
                    <?php elseif ($leaseType == 'receivable') : ?>
                        <th style="text-align:center">Received Date </th>
                        <th style="text-align:center">Received Method</th>
                    <?php endif; ?>

                    <th style="text-align:right">Referance Number</th>
                    <th style="text-align:center">Date From </th>
                    <th style="text-align:center">Date To </th>
                    <th style="text-align:center">Amount </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $paymentAmountTotal =  0;
                foreach ($payments as $k => $val) :
                ?>
                    <tr>
                        <td style="text-align:center"><?php echo  ++$k; ?></td>
                        <?php if ($leaseType == 'payable') : ?>
                            <td style="text-align:center"><?php echo  $val->PAYMENT_DATE; ?></td>
                            <td style="text-align:center"><?php echo  $val->PAYMENT_METHOD; ?></td>
                        <?php elseif ($leaseType == 'receivable') : ?>
                            <td style="text-align:center"><?php echo  $val->RECEIVED_DATE; ?></td>
                            <td style="text-align:center"><?php echo  $val->RECEIVED_METHOD; ?></td>
                        <?php endif; ?>

                        <td style="text-align:right"><?php echo  $val->REFERENCE_NUMBER; ?></td>
                        <td style="text-align:center"><?php echo  $val->DATE_FROM; ?></td>
                        <td style="text-align:center"><?php echo  $val->DATE_TO; ?></td>
                        <td style="text-align:right"><?php echo  number_format($val->AMOUNT); ?></td>
                    </tr>
                <?php
                    $paymentAmountTotal += $val->AMOUNT;
                endforeach;
                ?>
            <tfoot style="background-color:#E6E6FA">
                <tr>
                    <td colspan="6" style="text-align:left; font-weight:bold">TOTAL</td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format($paymentAmountTotal); ?></td>

                </tr>
                <tr>
                    <td colspan="6" style="text-align:left; font-weight:bold">Total Outstanding</td>
                    <td style="text-align:right; font-weight:bold"><?php echo number_format($rentSladAmountTotal - ($paymentAmountTotal + $advanceSladAmountTotal)) ?></td>

                </tr>
            </tfoot>
            </tbody>
        </table>';
        <!-- End Location -->
    </div>
</body>

</html>