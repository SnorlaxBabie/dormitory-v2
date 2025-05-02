<script>
    $(document).ready(function () {
        $(".pickdate").datepicker({
            dateFormat: "mm-dd-yy",
            minDate: 0 
        });

        const $picked_date = $('#pickdate');
        const $curr_date = $('#curr_date');

        $picked_date.change(function() {
            if ($picked_date.val()) { 
                $curr_date.prop('checked', true);
            } else {
                $curr_date.prop('checked', false);
            }
        });

        $curr_date.change(function() {
            if (this.checked) {
                const today = new Date();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const year = today.getFullYear();
                const formattedDate = `${month}-${day}-${year}`;
                $picked_date.val(formattedDate);
                $picked_date.prop('readonly', true);
            } else {
                $picked_date.val('');
                $picked_date.prop('readonly', false);
            }
        });

        const $picked_date1 = $('#pickdate1');
        const $curr_date1 = $('#curr_date1');

        $picked_date1.change(function() {
            if ($picked_date1.val()) { 
                $curr_date1.prop('checked', true);
            } else {
                $curr_date1.prop('checked', false);
            }
        });

        $curr_date1.change(function() {
            if (this.checked) {
                const today = new Date();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const year = today.getFullYear();
                const formattedDate = `${month}-${day}-${year}`;
                $picked_date1.val(formattedDate);
                $picked_date1.prop('readonly', true);
            } else {
                $picked_date1.val('');
                $picked_date1.prop('readonly', false);
            }
        });

    });
    
    function ajaxWithBlocking(options) {
        $.blockUI({
            message: `
            <div class="loader">
                <div class="loader-spinner"></div>
            </div>
            `,
            css: {
            border: 'none',
            backgroundColor: 'transparent',
            color: 'white',
            },
        });

        $.ajax(options).always(function() {
            $.unblockUI();
        });
    }

    function route(filename){
        window.location = `${filename}.php`;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
