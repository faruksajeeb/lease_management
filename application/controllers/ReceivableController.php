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
    public function createReceivable($data = NULL)
    {
        $this->webspice->user_verify($this->urlPrefix . 'login', $this->urlPrefix . 'create_receivable');
        $this->webspice->permission_verify('create_receivable');
        $data['url_prefix'] = $this->urlPrefix;
        if (!isset($data['edit'])) {
            $data['edit'] = array(
                'VENDOR_ID' => null,
                'LEASE_ID' => null,
                'PERIOD' => null,
                'AMOUNT' => null,
                'PAYMENT_METHOD_ID' => null,
                'RECEIVE_DATE' => null,
                'REFERENCE_NUMBER' => null,
                'REMARKS' => null,
                'RECEIVED_BY' => null,
                'ATTACHMENT' => null
            );
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('VENDOR_ID', 'Vendor Name', 'required|trim');
        $this->form_validation->set_rules('LEASE_ID', 'LEASE_ID', 'required|trim');
        $this->form_validation->set_rules('PERIOD', 'PERIOD', 'required');
        $this->form_validation->set_rules('AMOUNT', 'AMOUNT', 'required|trim');
        $this->form_validation->set_rules('PAYMENT_METHOD_ID', 'PAYMENT_METHOD_ID', 'trim');
        $this->form_validation->set_rules('RECEIVE_DATE', 'RECEIVE_DATE', 'required');

        if (!$this->form_validation->run()) {
            # for ajax call
            if (validation_errors()) {
                exit("Submit Error:\n" . strip_tags(validation_errors()));
            }

            $data['vendors'] = $this->db->query("SELECT * FROM tbl_vendor WHERE STATUS=7")->result();
            $data['leases'] = $this->db->query("SELECT * FROM tbl_lease_onboarding WHERE STATUS=7")->result();

            $this->load->view('receivable/create_receivable', $data);
            return FALSE;
        }

        # get input post
        $input = $this->webspice->get_input('user_id');

        if($input->AMOUNT<= 0){
            exit("Submit Error:\n Amount should be greater then 0" );
        }
        $period = $input->PERIOD;
        $periodSlice = explode("/",$period);
        $periodMonth = $periodSlice[0];
        $periodYear = $periodSlice[1];
        $customePeriod = date("Y-m-d",strtotime("$periodYear-$periodMonth-01"));
        
        $fileName = null;
        if ($_FILES['attachment']['name']) {
            $fileName = $this->webspice->get_safe_url($_FILES['attachment']['name']);

            $allowedExts = array("jpeg", "jpg", "png", "pdf");
            $this->webspice->check_file_type($allowedExts, 'attachment', $data, 'receivable');
        }

        $previous_uploaded_file = $input->previous_uploaded_attachment;

        if ($previous_uploaded_file != $fileName && ($fileName != null)) {
            if (file_exists($this->webspice->get_path('receivabel_full') . $input->key . '-' . $previous_uploaded_file)) {
                @unlink($this->webspice->get_path('receivabel_full') . $input->key . '-' . $previous_uploaded_file);
            }
        } else {
            $fileName = $previous_uploaded_file;
        }
        $time = time();
        if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name']) {
            $this->webspice->upload_file('attachment', $this->webspice->get_path('receivable_full'), $time . '-receivable-');
            $fileName = $time . '-receivable-' . $fileName;
        }
        
        # update process
        if ($input->key) {
            $updateData = array(
                'VENDOR_ID' => $input->VENDOR_ID,
                'LEASE_ID' => $input->LEASE_ID,
                'PERIOD' => $customePeriod,
                'RECEIVE_DATE' => $input->RECEIVE_DATE,
                'PAYMENT_METHOD_ID' => $input->PAYMENT_METHOD_ID,
                'REFERENCE_NUMBER' => $input->REFERENCE_NUMBER,
                'AMOUNT' => $input->AMOUNT,
                'REMARKS' => $input->REMARKS,
                'ATTACHMENT' => $fileName,
                'UPDATED_BY' => $this->webspice->get_user_id(),
                'UPDATED_DATE' => $this->webspice->now('datetime24')
            );
            $this->db->where('ID', $input->key);
            $this->db->update('tbl_receivable', $updateData);
            $this->webspice->log_me('Receivable_updated - ' . $input->key); # log activities
            exit('update_success');
        }

       
        # INSERT
        $insertData = array(
            'VENDOR_ID' => $input->VENDOR_ID,
            'LEASE_ID' => $input->LEASE_ID,
            'PERIOD' => $customePeriod,
            'RECEIVE_DATE' => $input->RECEIVE_DATE,
            'PAYMENT_METHOD_ID' => $input->PAYMENT_METHOD_ID,
            'REFERENCE_NUMBER' => $input->REFERENCE_NUMBER,
            'AMOUNT' => $input->AMOUNT,
            'REMARKS' => $input->REMARKS,
            'RECEIVED_BY' => $this->webspice->get_user_id(),
            'ATTACHMENT' => $fileName,
            'CREATED_BY' => $this->webspice->get_user_id(),
            'CREATED_DATE' => $this->webspice->now('datetime24')
        );

        # dd($insertData);
        try {
            $this->db->insert('tbl_receivable', $insertData);
            if ($this->db->insert_id()) {
                exit('insert_success');
            }
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }
    public function manageReceivable()
    {
        $this->webspice->user_verify($this->urlPrefix . 'login', $this->urlPrefix . 'manage_receivable');
        $this->webspice->permission_verify('manage_receivable');

        $this->load->database();
        $orderby = ' ORDER BY tbl_receivable.ID DESC';
        $groupby = null;
        $where = ' WHERE tbl_receivable.STATUS !=-1';
        $additional_where = ' tbl_receivable.STATUS !=-1';
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
		SELECT tbl_receivable.*,tbl_lease_onboarding.LEASE_NAME,tbl_vendor.VENDOR_NAME 
		FROM tbl_receivable 
        LEFT JOIN tbl_vendor ON tbl_vendor.ID=tbl_receivable.vendor_id
        LEFT JOIN tbl_lease_onboarding ON tbl_lease_onboarding.ID = tbl_receivable.lease_id
		";

        $countSQL = "
		SELECT COUNT(*) AS TOTAL_RECORD
		FROM tbl_receivable
		";

        # filtering records
       
        if ($this->input->get('filter')) {
           
            $temp_filter = '';
            if ($this->input->get('date_from') && $this->input->get('date_to')) {
                $DateFrom = $this->input->get('date_from');
                $DateTo = $this->input->get('date_to');
                $additional_where .= " AND tbl_receivable.RECEIVE_DATE BETWEEN '{$DateFrom}' AND '{$DateTo}'";
                $temp_filter = "RECEIVE_DATE Between - '" . $DateFrom . "' and '" . $DateTo . "'";
            } elseif ($this->input->get('date_from')) {
                $DateFrom = $this->input->get('date_from');
                $additional_where .= " AND tbl_receivable.RECEIVE_DATE LIKE '%" . $DateFrom . "%'";
                $temp_filter = "RECEIVE_DATE - " . $DateFrom . "";
            } elseif ($this->input->get('date_to')) {
                $DateTo = $this->input->get('date_to');
                $additional_where .= " AND tbl_receivable.RECEIVE_DATE LIKE '%" . $DateTo . "%'";
                $temp_filter = "RECEIVE_DATE - " . $DateTo . "";
            }

            $result = $this->webspice->filter_generator(
                $TableName = 'tbl_receivable',
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
            $result['filter'] ? $filter_by = ($temp_filter != '' ? $result['filter'] . ', ' . $temp_filter : $result['filter']) : $filter_by = $custom_filter_by;
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
                $this->webspice->edit_generator($TableName = 'tbl_receivable', $KeyField = 'ID', $key, $RedirectController = 'ReceivableController', $RedirectFunction = 'createReceivable', $PermissionName = 'craete_receivable,manage_receivable', $StatusCheck = null, $Log = 'edit_receivable');
                return false;
                break;

            case 'inactive':
                $this->webspice->action_executer($TableName = 'tbl_receivable', $KeyField = 'ID', $key, $RedirectURL = 'manage_receivable', $PermissionName = 'manage_receivable', $StatusCheck = 7, $ChangeStatus = -7, $RemoveCache = 'receivable', $Log = 'inactive_receivable');
                return false;
                break;

            case 'active':
                $this->webspice->action_executer($TableName = 'tbl_receivable', $KeyField = 'ID', $key, $RedirectURL = 'manage_receivable', $PermissionName = 'manage_receivable', $StatusCheck = -7, $ChangeStatus = 7, $RemoveCache = 'receivable', $Log = 'active_receivable');
                return false;
                break;
            case 'delete':
                $this->webspice->action_executer($TableName = 'tbl_receivable', $KeyField = 'ID', $key, $RedirectURL = 'manage_receivable', $PermissionName = 'manage_receivable', $StatusCheck = 1, $ChangeStatus = -1, $RemoveCache = 'receivable', $Log = 'delete_receivable');
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


    public function getLeaseIdByVendor()
    {
        $postData = $this->input->post();
        $response = array();
        // Select record
        $this->db->select('ID,LEASE_NAME');
        $this->db->where('VENDOR_ID', $postData['vendor_id']);
        $this->db->where('LEASE_TYPE', 'receivable');
        $q = $this->db->get('tbl_lease_onboarding');
        $response = $q->result_array();
        echo json_encode($response);
    }
    public function getOutstanding()
    {
        $postData = $this->input->post();
        $RENT_TOTAL = $this->db->query("SELECT SUM(AMOUNT) as RENT_TOTAL FROM tbl_lease_agreement WHERE LEASE_ID=? AND `TYPE`=?", array($postData['lease_id'], 'rent'))->row('RENT_TOTAL');
        $ADVANCE_TOTAL = $this->db->query("SELECT SUM(AMOUNT) as ADVANCE_TOTAL FROM tbl_lease_agreement WHERE LEASE_ID=? AND `TYPE`=?", array($postData['lease_id'], 'advance'))->row('ADVANCE_TOTAL');
        $RECEIVED_TOTAL = $this->db->query("SELECT SUM(AMOUNT) as RECEIVED_TOTAL FROM tbl_receivable WHERE LEASE_ID=?", array($postData['lease_id']))->row('RECEIVED_TOTAL');
        $TOTAL_OUTSTANDING = $RENT_TOTAL - ($ADVANCE_TOTAL + $RECEIVED_TOTAL);
        echo $TOTAL_OUTSTANDING;
    }
    public function getAdvanceAndPaymentInfoByLeaseId()
    {
        $postData = $this->input->post();
        $leaseID = $postData['LEASE_ID'];

        $leaseRentSlabs = $this->db->query("SELECT * FROM tbl_lease_agreement WHERE LEASE_ID=? AND `TYPE`=?", array($leaseID, 'rent'))->result();
        $advanceSlabs = $this->db->query("SELECT * FROM tbl_lease_agreement WHERE LEASE_ID=? AND `TYPE`=?", array($leaseID, 'advance'))->result();
        $payments = $this->db->query("SELECT * FROM tbl_receivable WHERE LEASE_ID=?", array($leaseID))->result();

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
            $paymentMethod = ($val->PAYMENT_METHOD_ID == 1) ? 'Cash' : (($val->PAYMENT_METHOD_ID == 2) ? 'Cheque' : 'Card');
            $html .= '<tr>
            <td style="text-align:center">' . ++$k . '</td>
            <td style="text-align:center">' . $val->PERIOD . '</td>            
            <td style="text-align:center">' . $paymentMethod . '</td>
            <td style="text-align:right">' . number_format($val->AMOUNT) . '</td>
            <td style="text-align:right">' . $val->RECEIVE_DATE . '</td>
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
