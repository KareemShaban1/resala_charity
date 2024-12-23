<!-- DataTables CSS -->
<link href="{{asset('backend/assets/css/vendor/dataTables.bootstrap5.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backend/assets/css/vendor/responsive.bootstrap5.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backend/assets/css/vendor/buttons.bootstrap5.css')}}" rel="stylesheet" type="text/css" />

<!-- DataTables JS -->
<script defer src="{{asset('backend/assets/js/vendor/jquery.dataTables.min.js')}}"></script>
<script defer src="{{asset('backend/assets/js/vendor/dataTables.bootstrap5.js')}}"></script>
<script defer src="{{asset('backend/assets/js/vendor/dataTables.responsive.min.js')}}"></script>
<script defer src="{{asset('backend/assets/js/vendor/responsive.bootstrap5.min.js')}}"></script>
<script defer src="{{asset('backend/assets/js/vendor/dataTables.buttons.min.js')}}"></script>
<script defer src="{{asset('backend/assets/js/vendor/buttons.bootstrap5.min.js')}}"></script>

<!-- SweetAlert2 -->
<!-- <script src="{{asset('backend/assets/js/vendor/sweetalert2.all.min.js')}}" defer></script> -->

<!-- Global DataTable Configuration -->
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        // Global DataTable defaults
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "_MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                infoEmpty: "No records available",
                infoFiltered: "(filtered from _MAX_ total records)",
                paginate: {
                    first: '<i class="mdi mdi-chevron-double-left"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    last: '<i class="mdi mdi-chevron-double-right"></i>'
                }
            },
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            order: [[0, 'desc']],
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Global delete function
        window.deleteRecord = function(id, routePrefix) {
            Swal.fire({
                title: '{{__("Are you sure?")}}',
                text: "{{__("You won't be able to revert this!")}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#727cf5',
                cancelButtonColor: '#d33',
                cancelButtonText: '{{__("Cancel")}}',
                confirmButtonText: '{{__("Yes, delete it!")}}',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/${routePrefix}/${id}`,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                $('.dataTable').DataTable().ajax.reload();
                                Swal.fire(
                                    'Deleted!',
                                    'Record has been deleted.',
                                    'success'
                                );
                            }
                        },
                        error: function(error) {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        };
    });
</script>
