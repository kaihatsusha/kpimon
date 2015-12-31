var billItemModal = (function () {
    // private propertise
    _modal = null;

    // public function
    return {
        setModal : function(modal) {
            _modal = modal;
        },
        showModal: function(dataset) {
            if (dataset == null) {
                $('#netbilldetail-item_no').val('');
                $('#netbilldetail-item_name').val('');
                $('#netbilldetail-price').val('');
                $('#netbilldetail-pay_date').val('');
                $('#netbilldetail-description').val('');
                $('#netbilldetail-delete_flag').val(0);
            } else {
                $('#netbilldetail-item_no').val(dataset.item_no);
                $('#netbilldetail-item_name').val(dataset.item_name);
                $('#netbilldetail-price').val(dataset.price);
                $('#netbilldetail-pay_date').val(dataset.pay_date);
                $('#netbilldetail-description').val(dataset.description);
                $('#netbilldetail-delete_flag').val(dataset.delete_flag);
            }
            _modal.find('.has-error').each(function() {
                $(this).removeClass('has-error');
            });
            _modal.modal('show');
        },
        deleteItem: function(itemNo) {
            $('#netbilldetail-item_no').val(itemNo);
            $('#btnDeleteBillItem').click();
        }
    };
}());