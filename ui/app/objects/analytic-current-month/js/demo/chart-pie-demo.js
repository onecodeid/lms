// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

jQuery(document).ready(function() {
    let dir = window.location.href.indexOf("localhost") > -1 ? "/zalfa-sales/" : "/"
    jQuery.getJSON(dir + 'api/report/stat/stat_sales_009?month=2022-01', function(a) {

        let dataB = { labels: [], datas: [] }
            // for (let omz of a.data[0]) {
            //     dataB.labels.push(omz.label)
            //     dataB.datas.push(omz.total)
            // }

        let dataC = { labels: [], datas: [] }
        for (let ctg of a.data[1]) {
            dataC.labels.push(ctg.category_name)
            dataC.datas.push(ctg.item_qty)
        }

        let x = Math.ceil(a.data[0][a.data[0].length - 1].dday / 7)
        let y = 0,
            z = 1,
            total, mindate, maxdate

        while (y < 31) {
            total = 0
            mindate = y + 1
            while (z <= x) {
                for (let w of a.data[0]) {
                    if (w.dday == (y + 1)) {
                        maxdate = w.dday
                        total += Math.round(w.total)
                    }
                }

                y++
                z++
            }

            dataB.labels.push(mindate + (maxdate == mindate ? "" : " - " + maxdate))
            dataB.datas.push(total)
            z = 1
        }
        console.log(dataB.labels)

        // Pie Chart Example
        // const data = {
        //   labels: ['Red', 'Orange', 'Yellow', 'Green', 'Blue'],
        //   datasets: [
        //     {
        //       label: 'Dataset 1',
        //       data: Utils.numbers(NUMBER_CFG),
        //       backgroundColor: Object.values(Utils.CHART_COLORS),
        //     }
        //   ]
        // };

        var ctx = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: dataC.labels,
                datasets: [{
                    data: dataC.datas,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'right'
                },
                cutoutPercentage: 80,
            },
        });

        // Area Chart Example
        var ctx = document.getElementById("myAreaChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataB.labels,
                datasets: [{
                    label: "Earnings",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: dataB.datas,
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                return 'Rp ' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });

        // let boundary = 'start'
        // let index = 2

        // utils.srand(8);

        // let x = ["Ahad", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
        // let y = a.data.total_per_days //

        // try {
        //     new Chart('chart-' + index, {
        //         type: 'line',
        //         data: {
        //             labels: x,
        //             datasets: [{
        //                 backgroundColor: utils.transparentize(presets.red),
        //                 borderColor: presets.red,
        //                 data: y,
        //                 label: 'Dataset',
        //                 fill: boundary
        //             }]
        //         },
        //         options: Chart.helpers.merge(options, {
        //             title: {
        //                 text: 'fill: ' + boundary,
        //                 display: false
        //             },
        //             legend: {
        //                 display: false,
        //                 position: 'top',
        //             },
        //             scales: {
        //                 yAxes: [{
        //                     ticks: {
        //                         beginAtZero: true
        //                     }
        //                 }]
        //             }
        //         })
        //     });
        // } catch (error) {
        //     console.log(error.message)
        // }


    })
})