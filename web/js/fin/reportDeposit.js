jQuery(document).ready(function() {
    if (jQuery('#depositBarChart').length > 0) {
        var arrDataset = [];
        arrDataset.push({
            label:'Deposit', data:CHART_DATA.term, alias:CHART_DATA.termAlias, strokeColor:'#00c0ef', pointColor:'#00c0ef', fillColor:'#00c0ef'
        });
        var barchartData = {canvas:'#depositBarChart', arrLabel:CHART_DATA.label, arrDataset:arrDataset};
        finReport.setDepositData('barchart', barchartData);
        finReport.drawDepositChart();
    }
});