<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Payment Deatail</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td colspan='5'>Payment ID : <?php echo $payment_info->ID?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Payment Date :  <?php echo $payment_info->PAYMENT_DATE?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Payment Method :  <?php echo $payment_info->PAYMENT_METHOD?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Ref. Number :  <?php echo $payment_info->REFERENCE_NUMBER?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Remarks :  <?php echo $payment_info->REMARKS?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Payment By :  <?php echo $this->customcache->user_maker($payment_info->PAID_BY, 'USER_NAME') ?></td>
                    </tr>
                    
                    <tr>
                        <th>Lease ID</th>
                        <th>Lease Name</th>
                        <th>Date From</th>
                        <th>Date To</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    foreach($records as $val): ?>
                    <tr>
                        <td><?php echo $val->LEASE_ID; ?></td>
                        <td><?php echo $val->LEASE_NAME; ?></td>
                        <td><?php echo $val->DATE_FROM; ?></td>
                        <td><?php echo $val->DATE_TO; ?></td>
                        <td><?php echo $val->AMOUNT; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>