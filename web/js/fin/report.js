var finReport = (function () {
    // private propertise
    _paymentData = {};

    // private functions
    function drawLineChart(canvas, arrLabel, arrDataset) {
        var lineChartOptions = {
            showScale: true,// Boolean - If we should show the scale at all
            scaleShowGridLines: true,//Boolean - Whether grid lines are shown across the chart
            scaleGridLineColor: "rgba(0,0,0,.05)",//String - Colour of the grid lines
            scaleGridLineWidth: 1,//Number - Width of the grid lines
            scaleShowHorizontalLines: true,//Boolean - Whether to show horizontal lines (except X axis)
            scaleShowVerticalLines: true,//Boolean - Whether to show vertical lines (except Y axis)
            bezierCurve: true,//Boolean - Whether the line is curved between points
            bezierCurveTension: 0.3,//Number - Tension of the bezier curve between points
            pointDot: true,//Boolean - Whether to show a dot for each point
            pointDotRadius: 3,//Number - Radius of each point dot in pixels
            pointDotStrokeWidth: 1,//Number - Pixel width of point dot stroke
            pointHitDetectionRadius: 5,//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
            datasetStroke: true,//Boolean - Whether to show a stroke for datasets
            datasetStrokeWidth: 1,//Number - Pixel width of dataset stroke
            datasetFill: false,//Boolean - Whether to fill the dataset with a color
            legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",//String - A legend template
            maintainAspectRatio: true,//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            scaleShowXLabels : 1,
            responsive: true,//Boolean - whether to make the chart responsive to window resizing
            tooltipTemplate: '<%if (label){%><%=label%>: <%}%><%= aliasValue %>',
            multiTooltipTemplate : '<%= aliasValue %>'
        };

        var datasets = [];
        for (var i = 0; i < arrDataset.length; i++) {
            var dataset = arrDataset[i];
            datasets.push({
                label: dataset.label,
                fillColor: "rgba(210, 214, 222, 1)",
                strokeColor: dataset.strokeColor,
                pointColor: dataset.pointColor,
                pointStrokeColor: "#c1c7d1",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: dataset.data,
            });
        }

        var lineChartCanvas = $(canvas).get(0).getContext('2d');
        var lineChart = new Chart(lineChartCanvas).Line({labels: arrLabel, datasets: datasets}, lineChartOptions);
        var outDatasets = lineChart.datasets;
        for (var i = 0; i < outDatasets.length; i++) {
            var points = outDatasets[i].points;
            var arrAlias = arrDataset[i].alias;
            for (var j = 0; j < points.length; j++) {
                points[j].aliasValue = arrAlias[j];
            }
        }
    }
    function drawSparklineChart(canvas, arrDataset) {
        var chartCanvas = $(canvas);
        var tooltipFormatter = function (sparkline, options, fields) {
            var mergedOptions = options.mergedOptions;
            var lineColor = mergedOptions.lineColor;
            var xalias = mergedOptions.xalias;
            var ix = fields.x;
            var y = mergedOptions.yalias[ix];
            console.log(y);
            $html = '';
            $html += '<span style="color:' + lineColor + '">&#9679;</span> ' + y;
            if (typeof xalias !== 'undefined') {
                return xalias[ix] + '<br/>' + $html + '<br/>';
            }
            return $html;
        };
        for (var i = 0; i < arrDataset.length; i++ ) {
            var dataset = arrDataset[i];
            dataset.options['tooltipFormatter'] = tooltipFormatter;
            chartCanvas.sparkline(dataset.points, dataset.options);
        }
    }

    // public function
    return {
        setPaymentData : function(key, value) {
            _paymentData[key] = value;
        },
        drawPaymentChart : function () {
            var linechart = _paymentData.linechart;
            if (typeof linechart !== 'undefined') {
                drawLineChart(linechart.canvas, linechart.arrLabel, linechart.arrDataset);
            }

            var compositeInlineChart = _paymentData.compositeInlineChart;
            if (typeof compositeInlineChart !== 'undefined') {
                drawSparklineChart(compositeInlineChart.canvas, compositeInlineChart.arrDataset);
            }
        }
    };
}());