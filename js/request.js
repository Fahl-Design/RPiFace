var JMC = {};
JMC.Lights = {
    switches: function (selector) {
        $(selector).change(function () {
            var input = $(this);
            input.parents('tr').find('.status').html('<i class="material-icons">refresh</i>');
            var switchId = input.data('swtichid');
            var url = '/inc/request.php';
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    light: switchId
                },

                success: function (data) {
                    var rs = JSON.parse(data);
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
JMC.DHT = {
    room: function (selector) {
        $(selector).click(function () {
            var cavas = $('#canvas');
            cavas.empty();
            var input = $(this);
            var url = '/inc/request.php';
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    temp: 'yes'
                },

                success: function (data) {
                    var rs = JSON.parse(data);
                    var dataItems = {
                        labels: [],
                        datasets: [
                            {
                                label: "temperature",
                                fillColor : "rgba(220,220,220,0.5)",
                                strokeColor : "rgba(220,220,220,0.8)",
                                highlightFill: "rgba(220,220,220,0.75)",
                                highlightStroke: "rgba(220,220,220,1)",
                                data: []
                            },
                            {
                                label: "humidity",
                                fillColor: "rgba(151,187,205,0.5)",
                                strokeColor: "rgba(151,187,205,0.8)",
                                highlightFill: "rgba(151,187,205,0.75)",
                                highlightStroke: "rgba(151,187,205,1)",
                                data: []
                            }
                        ]
                    };
                    $('.roomTemp').empty();
                    $.each(rs, function(i, row) {

                        dataItems.labels.push(rs[i][0]);

                        dataItems.datasets[0]['data'].push(
                           rs[i][1]
                        );
                        dataItems.datasets[1]['data'].push(
                            rs[i][2]
                        );
                    });  // close each()

                    var ctx = document.getElementById("canvas").getContext("2d");

                    window.myLine = new Chart(ctx).Line(dataItems, {
                        responsive: true
                    });


                },
                error: function (error) {
                    console.log(rs);
                    input.parents('tr').find('.temp').html('<i class="material-icons">error_outline</i>')
                }
            })
        })
    },
    tempNow: function (selector) {

        var url = '/inc/request.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                tempNow: 'yes'
            },

            success: function (data) {
                var rs = JSON.parse(data);
                selector.append('Datum: '+rs[0]+' Temperature: '+rs[1]+'*C Humidity: '+rs[2]+'%');
            },
            error: function (error) { }
        })
    },
    tempTable: function (selector) {

        var url = '/inc/request.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                tempNow: 'yes'
            },

            success: function (data) {
                var rs = JSON.parse(data);
                selector.append('<tr><td>Datum: '+rs[0]+'</td><td>'+rs[1]+'*C</td><td>'+rs[2]+'%</td></tr>');
            },
            error: function (error) { }
        })
    }
};

$(function () {
    if ($('.mdl-switch__input').length > 0) {
        JMC.Lights.switches('.mdl-switch__input');
    }

    JMC.DHT.room($('.refreshTemp'));
    JMC.DHT.tempTable($('.temperatureTable'));

    JMC.DHT.tempNow($('.temperatureNow'));
});