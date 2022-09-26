<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Received Deatail</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td colspan='5'>Received ID : <?php echo $received_info->ID?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Received Date :  <?php echo $received_info->RECEIVED_DATE?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Received Method :  <?php echo $received_info->RECEIVED_METHOD?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Ref. Number :  <?php echo $received_info->REFERENCE_NUMBER?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Remarks :  <?php echo $received_info->REMARKS?></td>
                    </tr>
                    <tr>
                        <td colspan='5'>Received By :  <?php echo $this->customcache->user_maker($received_info->RECEIVED_BY, 'USER_NAME') ?></td>
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