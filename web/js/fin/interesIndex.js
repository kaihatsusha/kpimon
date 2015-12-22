jQuery(document).ready(function() {
    if (jQuery('#interestUnitAreaChart').length > 0) {
        var arrDataset = [];
        arrDataset.push({
            label:'Unit', data:CHART_DATA.interestUnit, alias:CHART_DATA.interestUnitAlias, strokeColor:'#00c0ef', pointColor:'#00c0ef', fillColor:'#00c0ef'
        });
        var interestUnitAreaChartData = {canvas:'#interestUnitAreaChart', arrLabel:CHART_DATA.label, arrDataset:arrDataset};
        finReport.setInterestUnitData('areaChart', interestUnitAreaChartData);
        finReport.drawInterestUnitChart();
    }
});