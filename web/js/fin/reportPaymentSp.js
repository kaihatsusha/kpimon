jQuery(document).ready(function() {
    if (jQuery('#paymentCompositeInlineChart').length > 0) {
        var arrDataset = [];
        arrDataset.push({points:CHART_DATA.credit, options:{fillColor: false, lineColor: '#00c0ef', height: '250px', width: '94%', spotRadius: 3, lineWidth:2, valueSpots:{':': '#00a65a'}, minSpotColor:false, maxSpotColor:false, yalias:CHART_DATA.creditAlias, xalias:CHART_DATA.label}});
        arrDataset.push({points:CHART_DATA.debit, options:{fillColor: false, lineColor: '#dd4b39', height: '250px', width: '94%', spotRadius: 3, lineWidth:2, valueSpots:{':': '#00a65a'}, minSpotColor:false, maxSpotColor:false, yalias:CHART_DATA.debitAlias, composite: true}});

        var compositeInlineChart = {canvas:'#paymentCompositeInlineChart', arrDataset:arrDataset};
        finReport.setPaymentData('compositeInlineChart', compositeInlineChart);
        finReport.drawPaymentChart();
    }
});