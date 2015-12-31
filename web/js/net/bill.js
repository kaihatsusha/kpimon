jQuery(document).ready(function() {
    billItemModal.setModal($("#addItemModal"));
});

function addBillItem() {
    billItemModal.showModal(null);
}

function editBillItem(obj) {
    billItemModal.showModal($(obj).data("maps"));
}

function deleteBillItem(obj) {
    $jqObj = $(obj);
    if (confirm($jqObj.data('messageDeleteConfirm'))) {
        billItemModal.deleteItem($jqObj.data('itemNo'));
    };
}