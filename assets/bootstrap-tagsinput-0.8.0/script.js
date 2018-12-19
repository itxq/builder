$(document).ready(function () {
    $('input.bootstrap-tagsinput-role').tagsinput({
        tagClass: function (item) {
            return 'btn btn-info btn-sm';
        }
    });
});