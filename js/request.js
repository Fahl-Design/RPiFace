var JMC = {};
JMC.Lights = {
    switches: function (selector) {
        $(selector).change(function () {
            var input = $(this);
            input.parents('tr').find('.status').html('<i class="material-icons">refresh</i>');
            var switchId = input.data('swtichid');
            var url = '/new/request.php';
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    light: switchId
                },

                success: function (data) {
                    var rs = JSON.parse(data);
                    console.log(rs.state);
                    input.parents('tr').find('.status').html('<i class="material-icons">done</i>');

                    if (rs.state == "ON") {
                        setTimeout(
                            function () {
                                input.parents('tr').find('.status').html('<i class="material-icons">wb_incandescent</i>')
                            }, 750
                        );
                    } else {
                        setTimeout(
                            function () {
                                input.parents('tr').find('.status').html('<i class="material-icons">brightness_3</i>')
                            }, 750
                        );
                    }
                },
                error: function (error) {
                    input.parents('tr').find('.status').html('<i class="material-icons">error_outline</i>')
                }
            });
        });
    }
};

$(function () {
    if ($('.mdl-switch__input').length > 0) {
        JMC.Lights.switches('.mdl-switch__input');
    }
});