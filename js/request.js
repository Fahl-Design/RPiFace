var JMC = {};
JMC.Lights = {
    switches: function (selector) {
        $(selector).on('click', function () {
            var input = $(this);
            input.find('.status').html('<i class="material-icons">refresh</i>');
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
                    input.find('.status').html('<i class="material-icons">done</i>');

                    if (rs.state == "ON") {
                        setTimeout(
                            function () {
                                input.find('.status').html('<i class="material-icons">wb_incandescent</i>');
                                input.addClass('alert-warning');
                            }, 500
                        );
                    } else {
                        setTimeout(
                            function () {
                                input.find('.status').html('<i class="material-icons">brightness_3</i>');
                                input.removeClass('alert-warning');
                            }, 500
                        );
                    }
                },
                error: function (error) {
                    input.find('.status').html('<i class="material-icons">error_outline</i>')
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
                                fillColor: "rgba(220,220,220,0.5)",
                                strokeColor: "rgba(220,220,220,0.8)",
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
                    $.each(rs, function (i, row) {

                        dataItems.labels.push(rs[i][0]);

                        dataItems.datasets[0]['data'].push(
                            rs[i][1]
                        );
                        dataItems.datasets[1]['data'].push(
                            rs[i][2]
                        );
                    });  // close each()

                    var ctx = document.getElementById("canvas").getContext("2d");

                    window.myLine = new Chart(ctx).Bar(dataItems, {
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
                selector.append('Current Temperatures <br><small>Datum: ' + rs[0] + ' <br>Temperature: ' + rs[1] + '*C <br>Humidity: ' + rs[2] + '%</small>');
            },
            error: function (error) {
            }
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
                selector.append('<tr><td>Datum: ' + rs[0] + '</td><td>' + rs[1] + '*C</td><td>' + rs[2] + '%</td></tr>');
            },
            error: function (error) {
            }
        })
    }
};

$(function () {
    JMC.Lights.switches('.panel-body .statusPanel');

    JMC.DHT.room($('.refreshTemp'));
    JMC.DHT.tempTable($('.temperatureTable'));

    JMC.DHT.tempNow($('.temperatureNow'));
});