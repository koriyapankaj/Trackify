@extends('layouts.main')


@push('title')
<title>Payment Categories</title>
@endpush

@section('main-section')
<!-- Custom styles for this page -->
<link href="{{ asset('vendors/datatables/dataTables.bootstrap5.css') }}" rel="stylesheet">

<script>
    //open edit model with data
    function editPaymentCategory(id) {
    $.ajax({
            url: "{{ route('payment_category-edit', ['id' => '']) }}/" + id,
            type: "GET",
            success: function (data) {
                // Handle success
                $('#payment_categoryModalLabel').text("edit category");
                $('#payment_categoryid').val(data.id);
                $('#title').val(data.title);
                $('#code').val(data.code);
                $('#status').val(data.status);
                $('#payment_categoryModal').modal('show');
            },
            error: function () {
                // Handle error
                alert('An error occurred.');
            }
        });
    }

</script>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3" id="table-card-title">
        <h6 class="m-0 font-weight-bold text-primary">Payment Categories</h6>
        <div>
            <button class="btn btn-primary" id="addPaymentCategory" data-coreui-toggle="modal" data-coreui-target="#payment_categoryModal"><i class="fas fa-plus"></i></button>
            <button id="delete-selected" class="btn btn-danger"><i class="fas fa-trash"></i></button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered" id="payment_categoryTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="check-all"></th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
        </table>  
        </div>
    </div>
</div>


<!--payment_category-form-modal -->
<div class="modal fade" id="payment_categoryModal" tabindex="-1" role="dialog" aria-labelledby="payment_categoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="payment_categoryModalLabel">Payment Category form</h5>
        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('payment_category-save') }}" method="post">
        <div class="modal-body">
          <div class="form-group">
            @csrf
            <input type="hidden" name="id" id="payment_categoryid" value="0">
            <label for="title" class="col-form-label">Payment Category:</label>
            <input type="text" class="form-control" name="title" id="title" oninvalid="this.setCustomValidity('PaymentCategory field is required')" oninput="setCustomValidity('')" required>
          </div>
          <div class="form-group">
            <label for="status" class="col-form-label">Status:</label>
            <select name="status" id="status" class="form-control">
                <option value="1">Enable</option>
                <option value="0">Disable</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--payment_category-form-modal end-->

<!-- Page level plugins -->
<script src="{{ asset('vendors/datatables/dataTables.js') }}"></script>
<script src="{{ asset('vendors/datatables/dataTables.bootstrap5.js') }}"></script>


<script>
$(document).ready(function () {

    $('#addPaymentCategory').click(function(){
        $('#payment_categoryModalLabel').text("add category");
        $('#payment_categoryid').val(0);
        $('#title').val("");
        $('#status').val(0);
    });

    var table = $('#payment_categoryTable').DataTable({
        serverSide: true,
        ajax: "{{ route('payment_categories') }}",
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'status',
                name: 'status',
                    render: function (data) {
                    if (data === 1) {
                        return '<span class="badge me-1 bg-success">Enable</span>';
                    } else {
                        return '<span class="badge me-1 bg-danger">Disable</span>';
                    }
                }
            },
            { data: 'edit', name: 'edit', orderable: false, searchable: false },
            { data: 'delete', name: 'delete', orderable: false, searchable: false },
        ],
        // "order": [[0, "desc"]]
    });

    // Handle the "Check All" checkbox
    $('#check-all').click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });


    // Handle "Delete Selected" button click
    $('#delete-selected').click(function () {
        var selectedIds = [];
        $('input[name="payment_category_id[]"]:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            // alert("Please select at least one record to delete.");
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select at least one record to delete.',
            });
        } else {
            
           Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('payment_category.deleteSelected') }}",
                        type: "POST",
                        data: {
                            selectedIds: selectedIds,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (data) {

                            // Handle success
                            table.ajax.reload();
                            if(data.status == 'success')
                            {
                                Swal.fire(
                                'Deleted!',
                                'PaymentCategory has been deleted.',
                                'success'
                                );
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.message,
                                });
                            }
                        },
                        error: function (data) {
                            // Handle error
                            console.log(data);
                        }
                    });
                }
            });

        }
    });  
    

});
</script>


@if (Session::has('success'))
<script>
    Swal.fire(
    'PaymentCategory!',
    '{{Session::get("success")}}',
    'success'
    );
</script>
@php
Session::forget('success');
@endphp
@endif

@if (Session::has('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{Session::get("error")}}',
    });
</script>
@php
Session::forget('error');
@endphp
@endif

@endsection
