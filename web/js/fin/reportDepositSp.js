jQuery(document).ready(function() {
    if (jQuery('#interestBarChart').length > 0) {
        var arrDataset = [];
        arrDataset.push({points:CHART_DATA.term, options:{type: 'bar', height: '250px', width: '94%', barWidth:10, barSpacing:3, barColor:'#f39c12', yalias:CHART_DATA.termAlias, xalias:CHART_DATA.label}});

        var barSparklineChart = {canvas:'#interestBarChart', arrDataset:arrDataset};
        finReport.setDepositData('barSparklineChart', barSparklineChart);
        finReport.drawDepositChart();
    }
});