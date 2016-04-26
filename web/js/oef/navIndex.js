jQuery(document).ready(function() {
    jQuery('#btnGetChart').click();
});
function getNavChart(url) {
    // send data
    var sendData = {};
    sendData.trade_date_from = jQuery('#oefnav-trade_date_from').val();
    sendData.trade_date_to = jQuery('#oefnav-trade_date_to').val();
    sendData.max_items_chart = jQuery('#max-items-chart').val();
    jQuery.ajax({
        type: 'POST',
        url: url,
        data: sendData,
        success: function(responseData) {
            jQuery('#navChart').html(responseData);
        }
    });
}