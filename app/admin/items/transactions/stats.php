<?php
    /*
    Copyright (c) 2021, 2022 Daerik.com
    This script may not be copied, reproduced or altered in whole or in part.
    We check the Internet regularly for illegal copies of our scripts.
    Do not edit or copy this script for someone else, because you will be held responsible as well.
    This copyright shall be enforced to the full extent permitted by law.
    Licenses to use this script on a single website may be purchased from Daerik.com
    @Author: Daerik
    */

    /**
     * @var Router\Dispatcher $dispatcher
     * @var Admin\User        $admin
     */

    // Set Title
    $page_title = 'Stats: Transactions';

    // Start Header
    include('includes/header.php');
?>

<main class="page-content">
    <section id="view-table" role="region">
        <div id="page-title-btn">
            <h1><?php echo $page_title; ?></h1>
        </div>

        <div class="row">
            <div class="col-12">
                <div id="chart-container"></div>
            </div>
        </div>
    </section>
</main>

<?php include('includes/footer.php'); ?>

<script>
    $(function () {
        // Variable Defaults
        var container = document.getElementById('chart-container');

        // Fetch Sales Data
        $.ajax({
            method: 'post',
            dataType: 'json',
            success: function (response) {
                // Switch Status
                switch (response.status) {
                    case 'success':
                        // Loop Through Charts
                        Object.keys(response.charts).forEach(function (label) {
                            // Create Canvas
                            var canvas = document.createElement('canvas');

                            // Append Canvas
                            container.appendChild(canvas);

                            // Create Chart
                            new Chart(canvas.getContext('2d'), {
                                type: 'line',
                                data: {
                                    datasets: response.charts[label]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function (value, index, values) {
                                                    return value.toLocaleString('en-US', {
                                                        style: 'currency',
                                                        currency: 'USD',
                                                        minimumFractionDigits: 0,
                                                        maximumFractionDigits: 0
                                                    });
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top'
                                        },
                                        title: {
                                            display: true,
                                            text: label
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function (context) {
                                                    return context.parsed.y.toLocaleString('en-US', {
                                                        style: 'currency',
                                                        currency: 'USD'
                                                    });
                                                },
                                                title: function (context) {
                                                    var dateParts = context[0].label.split('-');
                                                    var year = dateParts[0];
                                                    var month = parseInt(dateParts[1], 10) - 1;

                                                    return (new Date(year, month)).toLocaleString('en-US', {
                                                        month: 'long',
                                                        year: 'numeric'
                                                    });
                                                }
                                            }
                                        }
                                    }
                                },
                                plugins: [
                                    {
                                        beforeInit: function (chart) {
                                            var colors = ['red', 'blue', 'green', 'yellow'];
                                            var datasets = chart.data.datasets;

                                            datasets.forEach(function (dataset, index) {
                                                var color = colors[index % colors.length];
                                                dataset.borderColor = Chart.helpers.color(color).rgbString();
                                                dataset.backgroundColor = Chart.helpers.color(color).alpha(0.5).rgbString();
                                                dataset.pointStyle = 'circle';
                                                dataset.pointRadius = 10;
                                                dataset.pointHoverRadius = 15;
                                            });
                                        }
                                    }
                                ]
                            });
                        });
                        break;
                    case 'error':
                        displayMessage(response.message || Object.keys(response.errors).map(function (key) {
                            return response.errors[key];
                        }).join('<br>'), 'alert', null);
                        break;
                    default:
                        displayMessage(response.message || 'Something went wrong.', 'alert');
                }
            }
        });
    });
</script>

<?php include('includes/body-close.php'); ?>

