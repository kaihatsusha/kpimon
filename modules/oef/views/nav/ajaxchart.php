<?php if ($chartData): ?>
    <div id="navAreaChart"></div>
    <script type="text/javascript">
        CHART_DATA = <?= $chartData; ?>;
        if (jQuery('#navAreaChart').length > 0) {
            var arrDataset = [];
            arrDataset.push({points:CHART_DATA.nav, options:{height: '200px', width: '100%', lineColor:'#f00', fillColor:'#ffa', minSpotColor: false, maxSpotColor:false, spotColor:'#77f', spotRadius:3, yalias:CHART_DATA.alias, xalias:CHART_DATA.label}});

            var linecustomSparklineChart = {canvas:'#navAreaChart', arrDataset:arrDataset};
            finReport.setInterestUnitData('linecustomSparklineChart', linecustomSparklineChart);
            finReport.drawInterestUnitChart();
        }
    </script>
<?php endif; ?>