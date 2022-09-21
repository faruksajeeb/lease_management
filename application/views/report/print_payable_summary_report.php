<?php
$url_prefix = $this->webspice->settings()->site_url_prefix;
$site_url = $this->webspice->settings()->site_url;
$domain_name = $this->webspice->settings()->domain_name;
$site_title = $this->webspice->settings()->site_title;
$total_column = 5;

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
                        <!-- <h2 style=" text-align: center;"><?php //echo $companyInfo->ORGANIZATION_NAME; ?></h2> -->
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
            <tr>
                <td colspan="<?php echo $total_column; ?>" align="center" style="text-align:center; padding:0px;">Report
                    Period:
                    <?php echo date("F, Y", strtotime($from_date)) . ' to ' . date("F, Y", strtotime($to_date)); ?>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $total_column; ?>" align="center" style="text-align:center; padding:0px;">Report
                    Date: <?php echo date("d F, Y"); ?></td>
            </tr>
            <tr>
                <td style="padding:0px;">&nbsp;</td>
            </tr>
        </table>

        <table width="100%" id="" class="tabel data_table" border='1' cellpadding="2" cellspacing="0">

            <tbody>
                <?php

                $grandTotalAmount =
                    $grandTotalPaid =
                    $grandTotalOutstanding = 0;

                foreach ($get_records as $sgrow => $vendor) { ?>
                    <tr style="background-color: #FDF2E9;">
                        <td colspan="<?php echo $total_column; ?>" style="padding:15px"><b>Vendor:
                                <?php echo ($vendor['vendor_name'] != '') ? $vendor['vendor_name'] : 'Not Assigned'; ?></b>
                        </td>
                    </tr>
                    <tr style="background-color: #EAECEE;">
                        <th>Lease ID</th>
                        <th>Lease Name</th>
                        <th>Total Amount (Tk)</th>
                        <th>Total Paid (Tk)</th>
                        <th>Outstanding (Tk)</th>
                    </tr>
                    <?php
                    $vendorTotal = $vendorPaidTotal = $vendorOutstandingTotal = 0;
                    foreach ($vendor['lease_records'] as $record) { ?>
                        <tr>
                            <td><?php echo $record->LeaseId; ?> </td>
                            <td><?php echo $record->LEASE_NAME; ?> </td>
                            <td style='text-align:right'> <?php echo number_format($record->TotalAmount, 2); ?> </td>
                            <td style='text-align:right'> <?php echo number_format($record->TotalPaid, 2); ?> </td>
                            <td style='text-align:right'> <?php echo number_format($record->Due, 2); ?> </td>
                        </tr>
                    <?php
                        $vendorTotal += $record->TotalAmount;
                        $vendorPaidTotal += $record->TotalPaid;
                        $vendorOutstandingTotal += $record->Due;
                    }
                    ?>

                    <tr>
                        <th colspan="2">Vendot total</th>
                        <th style='text-align:right;font-weight:bold'> <?php echo number_format($vendorTotal, 2); ?></th>
                        <th style='text-align:right;font-weight:bold'> <?php echo number_format($vendorPaidTotal, 2); ?></th>
                        <th style='text-align:right;font-weight:bold'> <?php echo number_format($vendorOutstandingTotal, 2); ?></th>

                    </tr>
                <?php
                    $grandTotalAmount += $vendorTotal;
                    $grandTotalPaid += $vendorPaidTotal;
                    $grandTotalOutstanding += $vendorOutstandingTotal;
                } // END Vendor
                ?>

                <tr>
                    <th colspan="2">TOTAL</th>
                    <th style='text-align:right;font-weight:bold'><?php echo number_format($grandTotalAmount, 2); ?></th>
                    <th style='text-align:right;font-weight:bold'><?php echo number_format($grandTotalPaid, 2); ?></th>
                    <th style='text-align:right;font-weight:bold'><?php echo number_format($grandTotalOutstanding, 2); ?></th>
                </tr>
            </tbody>
        </table>
        <!-- End Location -->
    </div>
</body>

</html>