jQuery(document).ready(function() {
    jQuery('#oefpurchase-purchase_type').change(function() {
        changePurchaseType();
    });
    // init Action
    changePurchaseType();
});

function changePurchaseType() {
    var type = jQuery('#oefpurchase-purchase_type').val();
    var sipDateObj = jQuery('#oefpurchase-sip_date').val('');
    var sipDateView = jQuery('.field-oefpurchase-sip_date');
    sipDateView.hide();
    sipDateObj.val('');
    if (type == 2) {
        var backup = sipDateObj.data('backup');
        sipDateObj.val(backup);
        sipDateView.show();
    }
}