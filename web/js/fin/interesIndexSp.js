jQuery(document).ready(function() {
    if (jQuery('#interestUnitAreaChart').length > 0) {
        var arrDataset = [];
        arrDataset.push({points:CHART_DATA.interestUnit, options:{height: '200px', width: '94%', lineColor:'#f00', fillColor:'#ffa', minSpotColor: false, maxSpotColor:false, spotColor:'#77f', spotRadius:3, yalias:CHART_DATA.interestUnitAlias, xalias:CHART_DATA.label}});

        var linecustomSparklineChart = {canvas:'#interestUnitAreaChart', arrDataset:arrDataset};
        finReport.setInterestUnitData('linecustomSparklineChart', linecustomSparklineChart);
        finReport.drawInterestUnitChart();
    }
});