<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class ReceivableController extends CI_Controller
{
    public $urlPrefix;
    function __construct()
    {
        parent::__construct();
        $this->urlPrefix = $this->webspice->settings()->site_url_prefix;
    }
    public $tableName = 'tbl_payment';

    public function createReceived($data = NULL)
    {
       
        $this->webspice->user_verify($this->urlPrefix . 'login', $this->urlPrefix . 'create_receivable');
        $this->webspice->permission_verify('create_receivable');
        if (!isset($data['edit'])) {
            $data['edit'] = array(
                'VENDOR_ID' => null,
                'LEASE_ID' => null,
                'PERIOD' => null,
                'AMOUNT' => null,
                'RECEIVED_METHOD' => null,
                'PAYMENT_DATE' => null,
                'REFERENCE_NUMBER' => null,
                'REMARKS' => null,
                'PAID_BY' => null,
                'ATTACHMENT' => null
            );
        }

        $this->load->library('form_validation');
        // $this->form_validation->set_rules('VENDOR_ID', 'Vendor Name', 'required|trim');
        $this->form_validation->set_rules('receive_amount[]', 'receive_amount', 'required|trim');
        // $this->form_validation->set_rules('PERIOD', 'PERIOD', 'required');
        // $this->form_validation->set_rules('AMOUNT', 'AMOUNT', 'required|trim');
        // $this->form_validation->set_rules('RECEIVED_METHOD_ID', 'RECEIVED_METHOD_ID', 'trim');
        // $this->form_validation->set_rules('PAYMENT_DATE', 'PAYMENT_DATE', 'required');

        if (!$this->form_validation->run()) {
            # for ajax call
            if (validation_errors()) {
                exit("Submit Error:\n" . strip_tags(validation_errors()));
            }

            $data['vendors'] = $this->db->query("SELECT * FROM tbl_vendor WHERE STATUS=7")->result();
            // $data['leases'] = $this->db->query("SELECT * FROM tbl_lease_onboarding WHERE STATUS=7")->result();

            $this->load->view('receivable/create_receivable', $data);
            return FALSE;
        }

        # get input post
        $input = $this->webspice->get_input('edit_id');
        $insertData = array();
        $validAmt = 0;
        $amountExceedError = null;
        for ($i = 0; $i < count($input->receive_amount); $i++) {
            $payAmount = $input->receive_amount[$i];
            if ($payAmount <= 0) {
                continue;
            }
            $validAmt++;
            if ($input->receivable[$i] < $input->receive_amount[$i]) {
                $slabNo = $i + 1;
                $amountExceedError .= "slab no: " . $slabNo . " receive amount exceeded maximum limit. \n";
                continue;
            }

            $insertData[] = array(
                'lease_slab_id' => $input->lease_slab_id[$i],
                'receivable' => $input->receivable[$i],
                'receive_amount' => $input->receive_amount[$i]
            );
        }
        # Check limitation exceed
        if ($amountExceedError) {
            exit($amountExceedError);
        }
        # Check pay amount
        if ($validAmt == 0) {
            exit("Submit Error: Minimum 1 slab amount should be greater then 0 for make a payment.");
        }
        $paymentData = array(
            'RECEIVED_DATE' => date('Y-m-d'),
            'RECEIVED_METHOD' => $input->RECEIVED_METHOD,
            'REFERENCE_NUMBER' => $input->REFERENCE_NUMBER,
            'REMARKS' => $input->REMARKS,
            'RECEIVED_BY' => $this->webspice->get_user_id(),
            'ATTACHMENT' => null,
            'HISTORY' => null,
            'CREATED_BY' =>  $this->webspice->get_user_id(),
            'CREATED_AT' => $this->webspice->now('datetime24')
        );
        
        try {
          
           
            $this->db->trans_begin();
            # Insert into payment table
            $this->db->insert('tbl_received', $paymentData);
            $paymentId = $this->db->insert_id();
           
            if ($paymentId) {
               
                $paymentDeatils = array();
                
                foreach ($insertData as $key => $val) :
                   
                    $lease_slab_id = $val['lease_slab_id'];
                    $receive_amount = $val['receive_amount'];
                    $paymentDeatils[] = array(
                        'RECEIVED_ID' => $paymentId,
                        'LEASE_SLAB_ID' => $lease_slab_id,
                        'AMOUNT' => $receive_amount
                    );
                   
                    # Update lease slab/agrement table
                    $this->db->query("UPDATE tbl_lease_agreement 
                    SET PAID_AMOUNT = PAID_AMOUNT+$receive_amount,
                    REF=CONCAT(IFNULL(REF, ''),?,','),
                    UPDATED_BY=?,
                    UPDATED_AT=? 
                    WHERE ID=? ",array(
                        $paymentId,
                        $this->webspice->get_user_id(),
                        $this->webspice->now('datetime24'),
                        $lease_slab_id
                    ));
                endforeach;
               
                # Insert into payment detail table
                $this->db->insert_batch('tbl_received_details', $paymentDeatils);
               
            }
            if ($this->db->trans_status() === FALSE) {
                # If not success
                $this->db->trans_rollback();
                exit("Something went wrong");
            } else {
                $this->db->trans_commit();
                exit("success");
            }
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }
    public function manageReceived()
    {
        $this->webspice->user_verify($this->urlPrefix . 'login', $this->urlPrefix . 'manage_receivable');
        $this->webspice->permission_verify('manage_receivable');

        $this->load->database();
        $orderby = ' ORDER BY tbl_received.ID DESC';
        $groupby = null;
        $where = ' WHERE tbl_received.STATUS = 1 ';
        $additional_where = ' tbl_received.STATUS = 1 ';
        $page_index = 0;
        $no_of_record = 10;
        $limit = ' LIMIT ' . $no_of_record;
        $filter_by = 'Last Created';
        $data['pager'] = null;
        $data['url_prefix'] = $this->urlPrefix;
        $criteria = $this->uri->segment(2);
        $key = $this->uri->segment(3);

        if ($criteria == 'page') {
            $page_index = (int)$key;
            $page_index < 0 ? $page_index = 0 : $page_index = $page_index;
        }

        $initialSQL = "
		SELECT tbl_received.*
		FROM tbl_received 
		";

        $countSQL = "
		SELECT COUNT(*) AS TOTAL_RECORD
		FROM tbl_received
		";

        # filtering records
        if ($this->input->get('filter')) {
            $temp_filter = '';
            if ($this->input->get('date_from') && $this->input->get('date_to')) {
                $DateFrom = $this->input->get('date_from');
                $DateTo = $this->input->get('date_to');
                $additional_where .= " AND tbl_received.PAYMENT_DATE BETWEEN '{$DateFrom}' AND '{$DateTo}'";
                $temp_filter = "PAYMENT_DATE Between - '" . $DateFrom . "' and '" . $DateTo . "'";
            } elseif ($this->input->get('date_from')) {
                $DateFrom = $this->input->get('date_from');
                $additional_where .= " AND tbl_received.PAYMENT_DATE LIKE '%" . $DateFrom . "%'";
                $temp_filter = "PAYMENT_DATE - " . $DateFrom . "";
            } elseif ($this->input->get('date_to')) {
                $DateTo = $this->input->get('date_to');
                $additional_where .= " AND tbl_received.PAYMENT_DATE LIKE '%" . $DateTo . "%'";
                $temp_filter = "PAYMENT_DATE - " . $DateTo . "";
            }

            $result = $this->webspice->filter_generator(
                $TableName = 'tbl_received',
                $InputField = array('VENDOR_ID'),
                $Keyword = array('VENDOR_ID', 'LEASE_ID', 'PERIOD', 'AMOUNT'),
                $AdditionalWhere = $additional_where,
                $DateBetween = array()
            );

            $result['where'] ? $where = $result['where'] : $where = $where;
            if ($temp_filter == '') {
                $custom_filter_by = $filter_by;
            } elseif ($filter_by == 'Last Created' && $temp_filter != '') {
                $custom_filter_by = $temp_filter;
            } elseif ($temp_filter != '') {
                $custom_filter_by = $filter_by . ' ' . $temp_filter;
            }

            $result['filter'] ? $filter_by = $result['filter'] : $filter_by = $filter_by;
            $limit = null;
        }

        # action area
        switch ($criteria) {
            case 'print':
            case 'csv':
                if (!isset($_SESSION['sql']) || !$_SESSION['sql']) {
                    $_SESSION['sql'] = $initialSQL . $where . $groupby . $orderby . $limit;
                    $_SESSION['filter_by'] = $filter_by;
                }

                $record = $this->db->query($_SESSION['sql']); # print all record in the query
                $data['get_record'] = $record->result();
                $data['filter_by'] = $_SESSION['filter_by'];

                $this->load->view('receivable/print_receivable', $data);
                return false;
                break;

            case 'edit':

                $this->webspice->edit_generator($TableName = 'tbl_received', $KeyField = 'ID', $key, $RedirectController = 'PayableController', $RedirectFunction = 'createPayable', $PermissionName = 'craete_receivable,manage_receivable', $StatusCheck = null, $Log = 'edit_receivable');

                return false;
                break;

            case 'inactive':
                $this->webspice->action_executer($TableName = 'tbl_received', $KeyField = 'ID', $key, $RedirectURL = 'manage_receivable', $PermissionName = 'manage_receivable', $StatusCheck = 7, $ChangeStatus = -7, $RemoveCache = 'receivable', $Log = 'inactive_receivable');
                return false;
                break;

            case 'active':
                $this->webspice->action_executer($TableName = 'tbl_received', $KeyField = 'ID', $key, $RedirectURL = 'manage_receivable', $PermissionName = 'manage_receivable', $StatusCheck = -7, $ChangeStatus = 7, $RemoveCache = 'receivable', $Log = 'active_receivable');
                return false;
                break;
            case 'delete':
                $this->webspice->action_executer($TableName = 'tbl_received', $KeyField = 'ID', $key, $RedirectURL = 'manage_receivable', $PermissionName = 'manage_receivable', $StatusCheck = 1, $ChangeStatus = -1, $RemoveCache = 'receivable', $Log = 'delete_receivable');
                return false;
                break;
        }

        # default
        $sql = $initialSQL . $where . $groupby . $orderby . $limit;

        # start: only for pager -- mysql
        if ($criteria == 'page' && !$this->input->get('filter')) {
            if (!isset($_SESSION['sql']) || !$_SESSION['sql']) {
                $sql = $sql;
            }
            $limit = sprintf("LIMIT %d, %d", $page_index, $no_of_record); # this is to avoid SQL Injection
            $sql = substr($_SESSION['sql'], 0, strpos($_SESSION['sql'], 'LIMIT'));
            $sql = $sql . $limit;
        }

        # load all records
        if (!$this->input->get('filter')) {
            $count_data = $this->db->query($countSQL)->row();
            $data['pager'] = $this->webspice->pager($count_data->TOTAL_RECORD, $no_of_record, $page_index, $this->urlPrefix . 'manage_receivable/page/', 10);
        }
        # end: only for pager -- mysql

        $_SESSION['sql'] = $sql;
        $_SESSION['filter_by'] = $filter_by;
        $result = $this->db->query($sql)->result();

        $data['get_record'] = $result;
        $data['filter_by'] = $filter_by;

        $this->webspice->log_me('viewed_option_list'); # log
        $data['vendors'] = $this->db->query("SELECT * FROM tbl_vendor WHERE STATUS=7")->result();
        $data['leases'] = $this->db->query("SELECT * FROM tbl_lease_onboarding WHERE STATUS=7")->result();

        $this->load->view('receivable/manage_receivable', $data);
    }

    // public function getLeaseIdByVendor()
    // {
    //     $postData = $this->input->post();
    //     $response = array();
    //     // Select record
    //     $this->db->select('ID,LEASE_NAME');
    //     $this->db->where('VENDOR_ID', $postData['vendor_id']);
    //     $this->db->where('LEASE_TYPE', 'receivable');
    //     $q = $this->db->get('tbl_lease_onboarding');
    //     $response = $q->result_array();
    //     echo json_encode($response);
    // }
    public function viewReceivable(){
        $id = $this->input->post('id');
        $id = $this->webspice->encrypt_decrypt($id, 'decrypt');
        $data['received_info'] = $this->db->query("SELECT * FROM tbl_received WHERE ID=$id")->row();
        $data['records'] = $this->db->query("SELECT R.RECEIVED_DATE,LA.LEASE_ID,LO.LEASE_NAME,LA.DATE_FROM,LA.DATE_TO,RD.AMOUNT 
        FROM tbl_received_details RD 
        LEFT JOIN tbl_received R ON R.ID=RD.RECEIVED_ID 
        LEFT JOIN tbl_lease_agreement LA ON LA.ID=RD.LEASE_SLAB_ID 
        LEFT JOIN tbl_lease_onboarding LO ON LO.ID=LA.LEASE_ID 
        WHERE R.ID=$id
        ")->result();
        $this->load->view('receivable/view_receivable',$data);
    }
    public function getLeaseIdByVendor()
    {
        $postData = $this->input->post();
        $vendorId = $postData['vendor_id'];
        $monthYear = $postData['month_year'];
        $Slice = explode("/", $monthYear);
        $Month = $Slice[0];
        $Year = $Slice[1];
        $dateFrom = date("Y-m-d", strtotime("$Year-$Month-01"));
        # Select record
        // $this->db->select('tbl_lease_onboarding.ID,tbl_lease_onboarding.LEASE_NAME,tbl_lease_agreement.DATE_FROM,tbl_lease_agreement.DATE_TO,tbl_lease_agreement.AMOUNT');
        // $this->db->from('tbl_lease_agreement');
        // $this->db->join('tbl_lease_onboarding', 'tbl_lease_onboarding.ID=tbl_lease_agreement.LEASE_ID', 'LEFT');
        // $this->db->where('tbl_lease_onboarding.VENDOR_ID', $vendorId);
        // $this->db->where('tbl_lease_onboarding.LEASE_TYPE', 'receivable');
        // $this->db->where('tbl_lease_agreement.TYPE', 'rent');
        // $q = $this->db->get();
        $q = $this->db->query("SELECT LA.ID,LO.ID as LeaseID,
        LO.LEASE_NAME,LA.DATE_FROM,
        LA.DATE_TO,LA.AMOUNT,
        (SELECT IFNULL(SUM(AMOUNT),0) as Advance FROM tbl_lease_agreement WHERE LEASE_ID=LO.ID AND `TYPE`='advance' AND (`DATE_FROM` BETWEEN LA.DATE_FROM AND LA.DATE_TO) AND (`DATE_TO` BETWEEN LA.DATE_FROM AND LA.DATE_TO)) as Advance,
        -- (SELECT IFNULL(SUM(AMOUNT),0) as Paid FROM tbl_received WHERE (`PERIOD` BETWEEN LA.DATE_FROM AND LA.DATE_TO) AND LEASE_ID=LO.ID AND VENDOR_ID=$vendorId GROUP BY VENDOR_ID,LEASE_ID,`PERIOD`) as Paid
        LA.PAID_AMOUNT as Paid
        FROM tbl_lease_agreement LA
        LEFT JOIN tbl_lease_onboarding LO ON LO.ID=LA.LEASE_ID
        WHERE 
        LO.VENDOR_ID=? AND 
        LO.LEASE_TYPE='receivable' AND 
        LA.DATE_TO <= ? AND
        LA.TYPE='rent' ", array($vendorId, $dateFrom));
        // echo $this->db->last_query();
        $response = $q->result();

        $html = "";
        $html .= "<table class='table table-brodered'>";
        $html .= "<thead style='font-weight:bold;background-color:#D5F5E3'>";
        $html .= "<th>Lease ID</th>";
        $html .= "<th>Lease Name</th>";
        $html .= "<th>Date From</th>";
        $html .= "<th>Date To</th>";
        $html .= "<th style='text-align:right'>Rent</th>";
        $html .= "<th style='text-align:right'>Advance</th>";
        $html .= "<th style='text-align:right'>Received</th>";
        $html .= "<th style='text-align:center' >Status</th>";
        $html .= "<th style='width:200px;text-align:right'>Payable</th>";
        $html .= "<th style='width:200px;text-align:right'>Pay</th>";
        $html .= "</thead>";
        $html .= "<tbody>";
        $totalReceivable = 0;
        $totalAmount = 0;
        $totalAdvance = 0;
        $totalPaid = 0;
        foreach ($response as $key => $val) :
            $amount = ($val->AMOUNT) ? $val->AMOUNT : 0;
            $advance = ($val->Advance) ? $val->Advance : 0;
            $paid = ($val->Paid) ? $val->Paid : 0;
            $payTotal = ($advance + $paid) ;
            $receivable = ($amount - $payTotal);
            if($receivable==0){
                $status = "<span class='badge badge-success'>Full Received</span>";
            }elseif($payTotal==0){
                $status = "<span class='badge badge-danger'>Due</span>";
            }elseif(($payTotal<$amount) && $payTotal>0){
                $status = "<span class='badge badge-warning'>Partial received</span>";
            }
            
            
            $html .= "<tr >";
            $html .= "<td>" . $val->LeaseID . "</td>";
            $html .= "<td>" . $val->LEASE_NAME . "</td>";
            $html .= "<td>" . $val->DATE_FROM . "</td>";
            $html .= "<td>" . $val->DATE_TO . "</td>";
            $html .= "<td style='text-align:right'>" . $amount . "</td>";
            $html .= "<td style='text-align:right'>" . $advance . "</td>";
            $html .= "<td style='text-align:right'>" . $paid . "</td>";
            $html .= "<td style='text-align:center'>" . $status . "</td>";
            $html .= "<td style='text-align:right'>
                    <input type='hidden' name='lease_slab_id[]' value='" . $val->ID . "'>
                    <input type='text' name='receivable[]' style='text-align:right' class='form-control receivable' readonly value='" . $receivable . "'>
                    </td>";
            $readOnly = ($receivable==0) ? 'readonly' : '';
            $readOnlyValue = ($receivable==0) ? 0 : '';
            $html .= "<td><input type='text' name='receive_amount[]' ".$readOnly." value='".$readOnlyValue."' onkeypress='return event.charCode >= 48 && event.charCode <= 57'  class='form-control pay' style='text-align:right'></td>";
            $html .= "</tr>";
            $totalReceivable += $receivable;
            $totalAmount += $amount;
            $totalAdvance += $advance;
            $totalPaid += $paid;
        endforeach;
        $html .= "<tr style='font-weight:bold;background-color:#D5F5E3'>
                <td colspan='4' style='text-align:right;font-weight:bold'>TOTAL</td>
                <td style='text-align:right;font-weight:bold'>" . $totalAmount . "</td>
                <td style='text-align:right;font-weight:bold'>" . $totalAdvance . "</td>
                <td style='text-align:right;font-weight:bold'>" . $totalPaid . "</td>
                <td style='text-align:right;font-weight:bold'></td>
                <td style='text-align:right;font-weight:bold'>" . $totalReceivable . "</td>
                <td id='pay_total'  style='text-align:right;font-weight:bold'></td>
            </tr>";
            $html .= "<tr style='font-weight:bold'>
            <td colspan='8' style='text-align:right;font-weight:bold'></td>
            <td colspan='2'>
            <select class='form-control' name='RECEIVED_METHOD' required>
                <option value=''>select received method</option>
                <option value='cash'>Cash</option>
                <option value='cheque'>Cheque</option>
                <option value='bank'>Bank</option>
            </select>
            </td>
        </tr>";
            $html .= "<tr style='font-weight:bold'>
            <td colspan='2' style='text-align:right;font-weight:bold'></td>
            <td colspan='1' style='text-align:right;font-weight:bold'>Remarks</td>
            <td colspan='5'><textarea class='form-control' name='REMARKS'></textarea></td>
            <td colspan='2'>
            <input type='text' name='REFERENCE_NUMBER' id='REFERENCE_NUMBER' class='form-control' placeholder='Enter Reference'>
            </td>
        </tr>";
        $html .= "<tr style='font-weight:bold'>
                <td colspan='8' style='text-align:right;font-weight:bold'></td>
                <td colspan='2'><input type='SUBMIT' value='SUBMIT' class='form-control btn btn-success submit_button' /></td>
            </tr>";
        $html .= "</tbody>";
        $html .= "</table";


        echo $html;
    }

    public function getOutstanding()
    {
        $postData = $this->input->post();
        $RENT_TOTAL = $this->db->query("SELECT SUM(AMOUNT) as RENT_TOTAL FROM tbl_lease_agreement WHERE LEASE_ID=? AND `TYPE`=?", array($postData['lease_id'], 'rent'))->row('RENT_TOTAL');
        $ADVANCE_TOTAL = $this->db->query("SELECT SUM(AMOUNT) as ADVANCE_TOTAL FROM tbl_lease_agreement WHERE LEASE_ID=? AND `TYPE`=?", array($postData['lease_id'], 'advance'))->row('ADVANCE_TOTAL');
        $PAID_TOTAL = $this->db->query("SELECT SUM(AMOUNT) as PAID_TOTAL FROM tbl_received WHERE LEASE_ID=?", array($postData['lease_id']))->row('PAID_TOTAL');
        $TOTAL_OUTSTANDING = $RENT_TOTAL - ($ADVANCE_TOTAL + $PAID_TOTAL);
        echo $TOTAL_OUTSTANDING;
    }
    public function getAdvanceAndPaymentInfoByLeaseId()
    {

        $postData = $this->input->post();
        $leaseID = $postData['LEASE_ID'];
        $leaseRentSlabs = $this->db->query("SELECT * 
        FROM tbl_lease_agreement 
        WHERE LEASE_ID=? AND `TYPE`=?", array($leaseID, 'rent'))->result();

        $advanceSlabs = $this->db->query("SELECT * FROM tbl_lease_agreement WHERE LEASE_ID=? AND `TYPE`=?", array($leaseID, 'advance'))->result();
        $payments = $this->db->query("SELECT * FROM tbl_received WHERE LEASE_ID=?", array($leaseID))->result();

        $html = '<table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="6"><h5 style="padding:5px;">Rent Slabs</h5></th>
            </tr>
            <tr>
                <th style="text-align:center">Sl No.</th>
                <th style="text-align:center">From </th>
                <th style="text-align:center">To</th>
                <th style="text-align:right">Amount</th>
                <th style="text-align:right">Amount With Tax</th>
                <th style="text-align:right">Amount With Vat</th>
            </tr>
        </thead>
        <tbody>';
        $rentSladAmountTotal =
            $rentSladAmountWithTaxTotal =
            $rentSladAmountWithVatTotal = 0;
        foreach ($leaseRentSlabs as $k => $val) :
            $html .= '<tr>
                <td style="text-align:center">' . ++$k . '</td>
                <td style="text-align:center">' . $val->DATE_FROM . '</td>
                <td style="text-align:center">' . $val->DATE_TO . '</td>
                <td style="text-align:right">' . number_format($val->AMOUNT) . '</td>
                <td style="text-align:right">' . number_format($val->AMOUNT_WITH_TAX) . '</td>
                <td style="text-align:right">' . number_format($val->AMOUNT_WITH_VAT) . '</td>
            </tr>';
            $rentSladAmountTotal += $val->AMOUNT;
            $rentSladAmountWithTaxTotal += $val->AMOUNT_WITH_TAX;
            $rentSladAmountWithVatTotal += $val->AMOUNT_WITH_VAT;
        endforeach;
        $html .= '<tfoot>
            <tr>
                <td colspan="3" style="text-align:left; font-weight:bold">TOTAL</td>
                <td style="text-align:right; font-weight:bold">' . number_format($rentSladAmountTotal) . '</td>
                <td style="text-align:right; font-weight:bold">' . number_format($rentSladAmountWithTaxTotal) . '</td>
                <td style="text-align:right; font-weight:bold">' . number_format($rentSladAmountWithVatTotal) . '</td>
            </tr>
        </tfoot>';


        $html .= '</tbody>
    </table>';
        $html .= '<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="6"><h5 style="padding:5px;">Advance</h5></th>
        </tr>
        <tr>
            <th style="text-align:center" >Sl No.</th>
            <th style="text-align:center" >From </th>
            <th style="text-align:center" >To</th>
            <th style="text-align:right">Amount</th>
            <th style="text-align:right">Amount With Tax</th>
            <th style="text-align:right">Amount With Vat</th>
        </tr>
    </thead>
    <tbody>';
        $advanceSladAmountTotal =
            $advanceSladAmountWithTaxTotal =
            $advanceSladAmountWithVatTotal = 0;
        foreach ($advanceSlabs as $k => $val) :
            $html .= '<tr>
            <td style="text-align:center">' . ++$k . '</td>
            <td style="text-align:center">' . $val->DATE_FROM . '</td>
            <td style="text-align:center">' . $val->DATE_TO . '</td>
            <td style="text-align:right">' . number_format($val->AMOUNT) . '</td>
            <td style="text-align:right">' . number_format($val->AMOUNT_WITH_TAX) . '</td>
            <td style="text-align:right">' . number_format($val->AMOUNT_WITH_VAT) . '</td>
        </tr>';
            $advanceSladAmountTotal += $val->AMOUNT;
            $advanceSladAmountWithTaxTotal += $val->AMOUNT_WITH_TAX;
            $advanceSladAmountWithVatTotal += $val->AMOUNT_WITH_VAT;
        endforeach;
        $html .= '<tfoot>
        <tr>
            <td colspan="3" style="text-align:left; font-weight:bold">TOTAL</td>
            <td style="text-align:right; font-weight:bold">' . number_format($advanceSladAmountTotal) . '</td>
            <td style="text-align:right; font-weight:bold">' . number_format($advanceSladAmountWithTaxTotal) . '</td>
            <td style="text-align:right; font-weight:bold">' . number_format($advanceSladAmountWithVatTotal) . '</td>
        </tr>
    </tfoot>';

        $html .= '</tbody>
</table>';

        $html .= '<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="6"><h5 style="padding:5px;">Payments</h5></th>
        </tr>
        <tr>
            <th style="text-align:center" >Sl No.</th>
            <th style="text-align:center" >Period </th>            
            <th style="text-align:center">Payment Method</th>
            <th style="text-align:center" >Amount </th>
            <th style="text-align:right">Received Date</th>
            <th style="text-align:right">Referance Number</th>
        </tr>
    </thead>
    <tbody>';
        $paymentAmountTotal =  0;
        foreach ($payments as $k => $val) :
            $paymentMethod = ($val->RECEIVED_METHOD_ID == 1) ? 'Cash' : (($val->RECEIVED_METHOD_ID == 2) ? 'Cheque' : 'Card');
            $html .= '<tr>
            <td style="text-align:center">' . ++$k . '</td>
            <td style="text-align:center">' . $val->PERIOD . '</td>            
            <td style="text-align:center">' . $paymentMethod . '</td>
            <td style="text-align:right">' . number_format($val->AMOUNT) . '</td>
            <td style="text-align:right">' . $val->PAYMENT_DATE . '</td>
            <td style="text-align:right">' . $val->REFERENCE_NUMBER . '</td>
        </tr>';
            $paymentAmountTotal += $val->AMOUNT;
        endforeach;
        $html .= '<tfoot>
        <tr>
            <td colspan="3" style="text-align:left; font-weight:bold">TOTAL</td>
            <td style="text-align:right; font-weight:bold">' . number_format($paymentAmountTotal) . '</td>
            <td style="text-align:right; font-weight:bold"></td>
            <td style="text-align:right; font-weight:bold"></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:left; font-weight:bold">Total Outstanding</td>
            <td style="text-align:right; font-weight:bold">' . number_format($rentSladAmountTotal - ($paymentAmountTotal + $advanceSladAmountTotal)) . '</td>
            <td style="text-align:right; font-weight:bold"></td>
            <td style="text-align:right; font-weight:bold"></td>
        </tr>
    </tfoot>';

        $html .= '</tbody>
</table>';
        echo $html;
    }
}
