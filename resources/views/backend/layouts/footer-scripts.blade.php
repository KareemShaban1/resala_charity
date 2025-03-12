 <!-- bundle -->
 <script src="{{asset('backend/assets/js/vendor.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/app.min.js')}}"></script>

 <!-- third party js -->
 <!-- <script src="{{asset('backend/assets/js/vendor/apexcharts.min.js')}}"></script> -->
 <script src="{{asset('backend/assets/js/vendor/jquery-jvectormap-1.2.2.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/jquery-jvectormap-world-mill-en.js')}}"></script>

 <!-- Datatables js -->
 <script src="{{asset('backend/assets/js/vendor/jquery.dataTables.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/dataTables.bootstrap5.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/dataTables.responsive.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/responsive.bootstrap5.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/dataTables.buttons.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/buttons.bootstrap5.min.js')}}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
 <script src="{{asset('backend/assets/js/vendor/buttons.html5.min.js')}} "></script>
 <script src="{{asset('backend/assets/js/vendor/buttons.flash.min.js')}}"></script>
 <script src="{{asset('backend/assets/js/vendor/buttons.print.min.js')}}"></script>
 <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script> <!-- ✅ Required for colvis -->


 <!-- Datatable Init js -->
 <!-- <script src="{{asset('backend/assets/js/pages/demo.datatable-init.js')}}"></script> -->

 <!-- third party js ends -->

 <!-- demo app -->
 <!-- <script src="{{asset('backend/assets/js/pages/demo.dashboard.js')}}"></script> -->

 <!-- SweetAlert2 -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script>
     const languages = {
         @if(App::getLocale() == 'en')
         en: {
             paginate: {
                 previous: "<i class='mdi mdi-chevron-left'></i> Previous",
                 next: "Next <i class='mdi mdi-chevron-right'></i>"
             },
             info: "Showing records _START_ to _END_ of _TOTAL_",
             lengthMenu: "Display _MENU_ records",
             search: "_INPUT_",
             searchPlaceholder: "Search...",
             zeroRecords: "No matching records found",
             infoEmpty: "No records to display",
             infoFiltered: "(filtered from _MAX_ total records)"
         },
         @else
         ar: {
             paginate: {
                 previous: "<i class='mdi mdi-chevron-right'></i> السابق",
                 next: "التالي <i class='mdi mdi-chevron-left'></i>"
             },
             info: "عرض السجلات من _START_ إلى _END_ من إجمالي _TOTAL_ سجلات",
             lengthMenu: "عرض _MENU_ سجلات",
             search: "_INPUT_",
             searchPlaceholder: "بحث...",
             zeroRecords: "لا توجد سجلات مطابقة",
             infoEmpty: "لا توجد سجلات للعرض",
             infoFiltered: "(تمت التصفية من إجمالي _MAX_ سجلات)"
         }
         @endif
     };

     const language = '{{ App::getLocale() }}';

     // Detect system language
     const systemLanguage = navigator.languages ? navigator.languages[0] : navigator.language;
     const isArabic = systemLanguage.startsWith('ar');


     // Listen for keydown events
     document.addEventListener('keydown', function(event) {
         if ((event.ctrlKey || event.metaKey)) {
             if (!isArabic && (event.key === 'c' || event.key === 'v')) {
                 event.stopPropagation(); // Allow copy-paste for English layout
             }
             const arabicKeyPattern = /[\u0600-\u06FF]/; // Arabic character Unicode range
             if (arabicKeyPattern.test(event.key)) {
                 console.log("Arabic keyboard detected!");
                 event.stopPropagation(); // Allow copy-paste for Arabic layout
             }

         }
     }, true);


     document.addEventListener("DOMContentLoaded", function() {
         fetchNotifications();
     });

     function fetchNotifications(date = new Date().toISOString().split('T')[0]) {
         fetch(`/notifications?date=${date}`)
             .then(response => response.json())
             .then(data => {
                 let notificationList = document.getElementById("notifications-list");

                 notificationList.innerHTML = "";

                 if (data.length === 0) {
                     notificationList.innerHTML = '<p class="text-center text-muted">No notifications for this date.</p>';
                     return;
                 }

                 data.forEach(notification => {
                     let notificationItem = `
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <div class="notify-icon bg-primary">
                            <i class="mdi mdi-calendar"></i>
                        </div>
                        <p class="notify-details">${notification.title}
                            <small class="text-muted">${notification.message}</small>
                        </p>
                    </a>`;
                     notificationList.innerHTML += notificationItem;
                 });
             })
             .catch(error => console.error("Error fetching notifications:", error));
     }
 </script>

 @stack('scripts')