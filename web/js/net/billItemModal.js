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

                // show Edit button
                _modal.find('#btnEditModal').show();
                // hide Delete button
                _modal.find('#btnDeleteModal').hide();
            } else {
                $('#netbilldetail-item_no').val(dataset.item_no);
                $('#netbilldetail-item_name').val(dataset.item_name);
                $('#netbilldetail-price').val(dataset.price);
                $('#netbilldetail-pay_date').val(dataset.pay_date);
                $('#netbilldetail-description').val(dataset.description);
                $('#netbilldetail-delete_flag').val(dataset.delete_flag);

                // hide submit
                _modal.find('#btnEditModal').hide();
                _modal.find('#btnDeleteModal').hide();
                var mode = dataset.mode;
                if (mode == 'edit') {
                    // show Edit button
                    _modal.find('#btnEditModal').show();
                } else if (mode == 'delete') {
                    // show Delete button
                    _modal.find('#btnDeleteModal').show();
                }
            }
            _modal.find('.has-error').each(function() {
                $(this).removeClass('has-error');
            });
            _modal.modal('show');
        }
    };
}());