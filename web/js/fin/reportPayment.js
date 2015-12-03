jQuery(document).ready(function() {
    if (jQuery('#paymentLineChart').length > 0) {
        var arrDataset = [];
        arrDataset.push({
            label:'Credit', data:CHART_DATA.credit, alias:CHART_DATA.creditAlias, strokeColor:'#00c0ef', pointColor:'#00c0ef'
        });
        arrDataset.push({
            label:'Debit', data:CHART_DATA.debit, alias:CHART_DATA.debitAlias, strokeColor:'#dd4b39', pointColor:'#dd4b39'
        });
        arrDataset.push({
            label:'Balance', data:CHART_DATA.balance, alias:CHART_DATA.balanceAlias, strokeColor:'#00a65a', pointColor:'#00a65a'
        });
        var linechartData = {canvas:'#paymentLineChart', arrLabel:CHART_DATA.label, arrDataset:arrDataset};
        finReport.setPaymentData('linechart', linechartData);
        finReport.drawPaymentChart();
    }
});