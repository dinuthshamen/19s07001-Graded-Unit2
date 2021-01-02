
<div class="container-fluid">

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Dashboard</a>
        </li>
        <li class="breadcrumb-item active"><?php echo $title; ?></li>
    </ol>

    <div class="row">
      <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Payment Summary</h5>
              <hr>

              <form id="frmFilterPayments">
                    <div class="form-row">
                          <div class="form-group col-md-3">
                          <label>Branch</label>
                                  <select class="form-control form-control-sm" name="branchId" id="branchId" required>
                                      <option value="">-- Select Branch --</option>
                                    <?php foreach ($branches as $branch) { ?>
                                      <option value="<?= $branch['id']; ?>"><?= $branch['name']; ?></option>
                                    <?php } ?>
                                  </select>
                          </div>
                          <div class="form-group col-md-3">
                          <label>Users</label>
                                  <select class="form-control form-control-sm" name="user" id="users">
                                      <option value="">-- Select User --</option>
                                  </select>
                          </div>
                          <div class="form-group col-md-2">
                              <label>Lecturer Precentage</label>
                              <div class="input-group mb-2">
                                <input type="number" name="L_precentage" id="L_precentage" class="form-control form-control-sm" placeholder="50" value="100" autocomplete="off" required >
                                <div class="input-group-prepend">
                                  <div class="input-group-text form-control-sm">%</div>
                                </div>
                              </div>               
                          </div>
                    </div>
                <div class="form-row">
                  <div class="form-group col-md-2">
                    <label>Date Range</label>
                    <input type="text" class="form-control form-control-sm" name="date" id="date" autocomplete="off" required>
                    <input type="hidden" name="startDate" id="startDate">
                    <input type="hidden" name="endDate" id="endDate">
                  </div>

                  <div class="form-group col-md-2">
                    <label>Student ID</label>
                    <input type="text" class="form-control form-control-sm" name="studentId" id="studentId">
                  </div>

                  <div class="form-group col-md-3">
                    <label>Filter by Course</label>
                    <select name="courseId" id="courseId" class="form-control form-control-sm">
                      <option value=""></option>
                      <?php foreach($courses as $course) { ?>
                        <option value="<?=$course['id']; ?>"><?=$course['name']; ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="form-group col-md-3">
                    <label>Filter by Batch</label>
                    <select name="batchId" id="batchId" class="form-control form-control-sm">
                      <option value=""></option>
                      <?php foreach($batches as $batch) { ?>
                        <option value="<?=$batch['id']; ?>"><?=$batch['name']; ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="form-group col-md-2">
                    <label>Fee Type</label>
                    <select name="fee_type" id="fee_type" class="form-control form-control-sm">
                      <option value="">All</option>
                      <option value="1">Course Fee</option>
                      <option value="2">Royalty / University Fee</option>
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm">Generate Report</button>
                    <button type="button" id="btnPrint" class="btn btn-warning btn-sm" disabled>Print Report</button>
                  </div>
                </div>
              </form>

              <hr>
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table id="tblTotal" class="table table-stripped">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>User</th>
                          <th>Student ID</th>
                          <th>Course</th>
                          <th>Fee Type</th>
                          <th>Payment Method</th>
                          <th>Action</th>
                          <th>Total</th>
                      </thead>

                      <tbody>

                      </tbody>
                    </table>
                  </div>
                  <!-- <button type="button" id="btnPrint" class="btn btn-warning btn-sm" >Print Report</button> -->
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
</div>

<script>
  $(function() {
   
    $('#date').daterangepicker({
        opens: 'right',
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()]
        },
        <?php
        $username = $this->session->userdata('username');
        if(!$this->user_model->validate_permission($username,16)) { ?>
          minDate: moment().subtract(6, 'days')
        <?php } ?>
    }, function(start, end, label) {
        $('#startDate').val(start.format('YYYY-MM-DD'));
        $('#endDate').val(end.format('YYYY-MM-DD'));
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
  });

  var t;
  $(document).ready(function() {
    $('#tblTotal').DataTable(); //data table 
    $('#date').val('');

      var buttonCommon = {
          exportOptions: {
              format: {
                  body: function ( data, row, column, node ) {
                      // Strip $ from salary column to make it numeric
                      return column === 5 ?
                          data.replace( /[$,]/g, '' ) :
                          data;
                  }
              }
          }
      };


      $('#branchId').bind('change',function() {
 
        var branchId = $("#branchId").val();

            $.ajax({
                  type : "POST",
                  //set the data type
                  url: '<?php echo base_url(); ?>index.php/payments/get_payments_users_by_branch', // target element(s) to be updated with server response
                  data: {branchId:branchId},
                  cache : false,
                  //check this in Firefox browser
                  success : function(response){
                    $('#users').children().remove().end()
                    $(' <option value="">-- Please Select --</option>').appendTo('#users');
                      $.each(response,function(key, val) {
                        
                          $('<option value='+val.username+'>'+val.username+'</option>').appendTo('#users');
                      });
                  }
              });
        });

      $('#btnPrint').click(function() {
        var branchId = $("#branchId").val();
        var username = $("#users").val();
        var L_precentage = $("#L_precentage").val();
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var studentId = $("#studentId").val();
        var courseId = $("#courseId").val();
        var batchId = $("#batchId").val();
        var fee_type = $("#fee_type").val();

        var printWindow = window.open('<?php echo base_url(); ?>index.php/payments/print_cashier_reports?studentId='+studentId+'&courseId='+courseId+'&batchId='+batchId+'&fee_type='+fee_type+'&startDate='+startDate+'&endDate='+endDate+'&branchId='+branchId+'&user='+username+'&L_precentage='+L_precentage, 'Print Report', 'height=1024,width=720');
      });

      $('#frmFilterPayments').submit(function(e) {
          e.preventDefault();
          var form = $('#frmFilterPayments');
          var l_precentage = parseFloat($('#L_precentage').val());
          var I_presentage = 100-l_precentage;

          $.blockUI();
          $.ajax({
           type: "GET",
           url: '<?php echo base_url(); ?>index.php/payments/filter_payments',
           data: form.serialize(),
           success: function(response) {
             var markup;
             var grand_total =0;

           

             $('#btnPrint').removeAttr('disabled');

             grand_total = parseFloat(grand_total);
             institute_total = parseFloat(grand_total);
             $.each(response,function(key, val) {
               var fee_type_name;
               if(val.fee_type==1) {
                 fee_type_name="Course Fees";
               } else {
                 fee_type_name="Royalty Fees";
               }


               grand_total+=parseFloat(val.amount);


               markup+="<tr><td>"+val.dateTime+"</td><td>"+val.username+"</td><td>"+val.studentId+"</td><td>"+val.courseName+"</td><td>"+fee_type_name+"</td><td>"+val.type+"</td><td><button onclick=delete_payment('"+val.studentId+"',"+val.pplanId+","+val.installmentId+") target='_blank' class='btn btn-danger btn-sm delete'><i class='fas fa-trash-alt'></i></button></td><td class='text-right'>"+parseFloat(val.amount).toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")+"</td></tr>";
             });
             var institution_total = grand_total*I_presentage/100;
             var Lecturer_total = grand_total*l_precentage/100;
             markup+="<tr><td colspan='5'><b>For institute "+I_presentage+"% </b></td><td class='text-right'><b>"+institution_total.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")+"</b></td></tr>";
             markup+="<tr><td colspan='5'><b>For Lecture "+l_precentage+"%</b></td><td class='text-right'><b>"+Lecturer_total.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")+"</b></td></tr>";
             markup+="<tr><td colspan='5'><b>Grand Total</b></td><td class='text-right' style='border-bottom: double; border-top: 1px solid'><b>"+grand_total.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")+"</b></td></tr>";

             $('#tblTotal tbody').html(markup);
             $.unblockUI();
           }
         });
      });

  } );

  function delete_payment(studentId,pplanId,installmentId){
    if(confirm('Are you sure you want to delete?') ) {
      $.blockUI();
      $.ajax({
       type: "POST",
       url: '<?php echo base_url(); ?>index.php/payments/delete_payment',
       data: { studentId: studentId, pplanId: pplanId, installmentId: installmentId },
       success: function(response) {
         if(response == "success") {
           $.notify({
            // options
            title: 'Payment deleted successfully',
            message: ''
            },{
            // settings
            type: 'success'
            });
            $('#frmFilterPayments').submit();
            $.unblockUI();
         } else {
           $.notify({
            // options
            title: 'Payment cannot be deleted!',
            message: 'May be you do not have necessary permission to delete payments'
            },{
            // settings
            type: 'danger'
            });
            $.unblockUI();
         }
       },
       error: function() {
         $.notify({
          // options
          title: 'Unexpected error occured!',
          message: 'Please contact system administrator'
          },{
          // settings
          type: 'danger'
          });
          $.unblockUI();
       }
    });
  }
}

</script>
