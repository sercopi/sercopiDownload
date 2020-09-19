$(document).ready(function() {
    $(".versionTable").each(function(index) {
        let table = $(this).DataTable({
            columnDefs: [
                {
                    targets: [0, 1],
                    searchable: true,
                    orderable: false,
                    className: "dt-body-center"
                }
            ],
            order: [[1, "asc"]]
        });
        let selectAll = $(this).find(".select-all");
        // Handle click on "Select all" control
        selectAll.on("click", function() {
            // Get all rows with search applied
            let rows = table.rows({ search: "applied" }).nodes();
            // Check/uncheck checkboxes for all rows in the table
            $('input[type="checkbox"]', rows).prop("checked", this.checked);
        });

        // Handle click on checkbox to set state of "Select all" control
        $(this).on("change", 'input[type="checkbox"]', function() {
            // If checkbox is not checked
            if (!this.checked) {
                let el = selectAll.get(0);
                // If "Select all" control is checked and has 'indeterminate' property
                if (el && el.checked && "indeterminate" in el) {
                    // Set visual state of "Select all" control
                    // as 'indeterminate'
                    el.indeterminate = true;
                }
            }
        });

        // Handle form submission event
        $("#form-versions").on("submit", function(e) {
            let form = this;

            // Iterate over all checkboxes in the table
            table.$('input[type="checkbox"]').each(function() {
                // If checkbox doesn't exist in DOM
                if (!$.contains(document, this)) {
                    // If checkbox is checked
                    if (this.checked) {
                        // Create a hidden element
                        $(form).append(
                            $("<input>")
                                .attr("type", "hidden")
                                .attr("name", this.name)
                                .val(this.value)
                        );
                    }
                }
            });
        });
    });
});
console.log("version table importado");
//https://www.gyrocode.com/articles/jquery-datatables-how-to-add-a-checkbox-column/
