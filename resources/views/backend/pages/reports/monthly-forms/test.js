<script>
  $(document).ready(function() {
    // Set default value for month_year as current month (YYYY-MM)
    let currentMonthYear = new Date().toISOString().slice(0, 7); // Format: YYYY-MM
    $('#month_year').val(currentMonthYear);

    // Event listener for the filter button
    $('#filterButton').on('click', function() {
        let month_year = $('#month_year').val();
        let department_id = $('#department_id').val();
        let follow_up_department_id = $('#follow_up_department_id').val();
        let monthly_form_status = $('#monthly_form_status').val();
        let area_id = $('#area_id').val();

        // Perform AJAX request to filter data
        $.ajax({
            url: "{{ route('monthly-forms-report.filter') }}",
            method: "GET",
            data: {
                month_year: month_year,
                department_id: department_id,
                follow_up_department_id: follow_up_department_id,
                status: monthly_form_status,
                area_id: area_id,
            },
            success: function(response) {
                // Update the tables with the filtered data
                $('#filteredData').html(response.filteredTable);
                $('#donorsWithFormsTable').html(response.donorsWithFormsTable);

                // Apply donor-specific filters after the data is loaded
                applyDonorFilters();
            }
        });
    });

    // Function to apply donor-specific filters
    function applyDonorFilters() {
        const filterDonorName = document.getElementById("filterDonorName");
        const filterArea = document.getElementById("filterArea");
        const filterStatus = document.getElementById("filterStatus");
        const rows = document.querySelectorAll(".donor-row");

        function applyFilters() {
            const nameValue = filterDonorName.value.toLowerCase();
            const areaValue = filterArea.value.toLowerCase();
            const statusValue = filterStatus.value;

            rows.forEach(row => {
                const donorName = row.getAttribute("data-donor-name");
                const area = row.getAttribute("data-area");
                const status = row.getAttribute("data-status");

                const matchesName = !nameValue || donorName.includes(nameValue);
                const matchesArea = !areaValue || area.includes(areaValue);
                const matchesStatus = !statusValue || status === statusValue;

                if (matchesName && matchesArea && matchesStatus) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }


        // Add event listeners for donor-specific filters
        filterDonorName.addEventListener("input", applyFilters);
        filterArea.addEventListener("input", applyFilters);
        filterStatus.addEventListener("change", applyFilters);

        // Apply filters immediately after the data is loaded
        applyFilters();
    }
});

</script>