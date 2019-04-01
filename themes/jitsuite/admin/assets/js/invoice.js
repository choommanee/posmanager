$(document).ready(function () {
    if (!localStorage.getItem('qaref')) {
        localStorage.setItem('qaref', '');
    }

    var $customer = $('#slcustomer');
    $customer.change(function (e) {
        localStorage.setItem('slcustomer', $(this).val());
        $('#slcustomer_id').val($(this).val());
    });
    if (slcustomer = localStorage.getItem('slcustomer')) {
        $customer.val(slcustomer).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url+"customers/getCustomer/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (typeof data.results !== 'undefined') {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    } else {
        nsCustomer();
    }


    ItemnTotals();
    $('.bootbox').on('hidden.bs.modal', function (e) {
        $('#add_item').focus();
    });
    $('body a, body button').attr('tabindex', -1);
    check_add_item_val();
    if (site.settings.set_focus != 1) {
        $('#add_item').focus();
    }

    //localStorage.clear();
    // If there is any item in localStorage
    if (localStorage.getItem('qaitems')) {
        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('slitems')) {
                    localStorage.removeItem('slitems');
                }
                if (localStorage.getItem('qaitems')) {
                    localStorage.removeItem('qaitems');
                }
                if (localStorage.getItem('qaref')) {
                    localStorage.removeItem('qaref');
                }
                if (localStorage.getItem('qawarehouse')) {
                    localStorage.removeItem('qawarehouse');
                }
                if (localStorage.getItem('qanote')) {
                    localStorage.removeItem('qanote');
                }
                if (localStorage.getItem('qadate')) {
                    localStorage.removeItem('qadate');
                }
                if (localStorage.getItem('edate')) {
                    localStorage.removeItem('edate');
                }
                if (localStorage.getItem('invoice_type')) {
                    localStorage.removeItem('invoice_type');
                }
                 if (localStorage.getItem('slref')) {
                    localStorage.removeItem('slref');
                }
                 if (localStorage.getItem('slcustomer')) {
                    localStorage.removeItem('slcustomer');
                }
                $('#modal-loading').show();
                location.reload();
            }
        });
    });

    $('#invoice_type').change(function (e) {
    localStorage.setItem('invoice_type', $(this).val());
    });
    if (invoice_type = localStorage.getItem('invoice_type')) {
        $('#invoice_type').val(invoice_type);
    }

    $('#qadate').change(function (e) {
        localStorage.setItem('qadate', $(this).val());
        });
    if (qadate = localStorage.getItem('qadate')) {
        $('#qadate').val(qadate);
    }

    $('#edate').change(function (e) {
    localStorage.setItem('edate', $(this).val());
    });
    if (edate = localStorage.getItem('edate')) {
        $('#edate').val(edate);
    }

    $('#slcustomer_id').change(function (e) {
        localStorage.setItem('slcustomer', $(this).val());
    });
    if (slcustomer_id = localStorage.getItem('slcustomer')) {
        $('#slcustomer_id').val(slcustomer_id);
    }

    $('#slref').change(function (e) {
    localStorage.setItem('slref', $(this).val());
    });
    if (slref = localStorage.getItem('slref')) {
        $('#slref').val(slref);
    }

    // save and load the fields in and/or from localStorage
    $('#qaref').change(function (e) {
        localStorage.setItem('qaref', $(this).val());
    });
    if (qaref = localStorage.getItem('qaref')) {
        $('#qaref').val(qaref);
    }
    $('#qawarehouse').change(function (e) {
        localStorage.setItem('qawarehouse', $(this).val());
    });
    if (qawarehouse = localStorage.getItem('qawarehouse')) {
        $('#qawarehouse').select2("val", qawarehouse);
    }

    //$(document).on('change', '#qanote', function (e) {
        $('#qanote').redactor('destroy');
        $('#qanote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('qanote', v);
            }
        });
        if (qanote = localStorage.getItem('qanote')) {
            $('#qanote').redactor('set', qanote);
        }

    // prevent default action upon enter
    $('body').bind('keypress', function (e) {
        if ($(e.target).hasClass('redactor_editor')) {
            return true;
        }
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

    $(document).on('click', '.qadel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        //alert(item_id);
        delete qaitems[item_id];
        row.remove();
        if(qaitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('qaitems', JSON.stringify(qaitems));
            loadItems();
            return;
        }
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */

    $(document).on("change", '.rquantity', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        qaitems[item_id].row.qty = new_qty;
        localStorage.setItem('qaitems', JSON.stringify(qaitems));
        loadItems();
    });

    $(document).on("change", '.rtype', function () {
        var row = $(this).closest('tr');
        var new_type = $(this).val(),
        item_id = row.attr('data-item-id');
        qaitems[item_id].row.type = new_type;
        localStorage.setItem('qaitems', JSON.stringify(qaitems));
    });

    $(document).on("change", '.rvariant', function () {
        var row = $(this).closest('tr');
        var new_opt = $(this).val(),
        item_id = row.attr('data-item-id');
        qaitems[item_id].row.option = new_opt;
        localStorage.setItem('qaitems', JSON.stringify(qaitems));
    });


});


/* -----------------------
 * Load Items to table
 ----------------------- */

function loadItems() {

    if (localStorage.getItem('qaitems')) {
        count = 1;
        countTotal =0;
        total_pay_bill = 0;
        an = 1;
        $("#slTable tbody").empty();
        qaitems = JSON.parse(localStorage.getItem('qaitems'));
        //alert(site.settings.item_addition);
        sortedItems = (site.settings.item_addition == 1) ? _.sortBy(qaitems, function(o){return [parseInt(o.order)];}) : qaitems;
        $.each(sortedItems, function () {
            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
            //alert(item_id)
            item.order = item.order ? item.order : new Date().getTime();
            var sl_id = item.row.id, reference_no = item.row.reference_no, balance = item.row.balance, bill_status = item.row.bill_status, biller = item.row.biller, customer=item.row.customer, date=item.row.date, due_date=item.row.due_date ,grand_total =item.row.grand_total;
            var type = item.row.type ? item.row.type : '';
            var payment_status =  item.row.payment_status, payment_term=item.row.payment_term;
            var sale_status = item.row.sale_status , paid =item.row.paid;
            var item_pay_total = item.row.item_pay_total;
           // var totalPaybill =
            var row_no = (new Date).getTime();

            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td class="text-center"><input name="sl_id[]" type="hidden" class="rid" value="' + sl_id + '"><span class="sname" id="name_' + row_no + '">' + an+'</span></td>';
            tr_html += '<td class="text-center"><input name="reference_no[]" type="hidden" class="rid" value="' + reference_no + '">'+reference_no+'</td>';
            tr_html += '<td class="text-center"><input name="dateCreate[]" type="hidden" class="rid" value="' + date + '">'+date+'</td>';
            tr_html += '<td class="text-center">ขายเช่ือ</td>';
            tr_html += '<td class="text-center">'+due_date+'</td>';
            tr_html += '<td class="text-right"><input name="payment_term[]"  class="form-control "  type="hidden" value="' + payment_term + '"><input name="totalAmount[]"  class="form-control "  type="hidden" value="' + formatMoney(grand_total) + '">'+formatMoney(grand_total)+'</td>';
            tr_html +='<td><input name="totalpaybill[]"  class="form-control input-sm text-right rprice"  type="text" value="' + formatMoney(item_pay_total) + '"></td>';
            tr_html += '<td class="text-center"><i class="fa fa-times tip qadel" id="' + item_id + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.appendTo("#slTable");
           // tr_From.prependTo("#qaTable");
            countTotal += parseFloat(grand_total);
            total_pay_bill += parseFloat(item_pay_total);
            an++;
            count++;

        });

        var col = 5;
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-right">' + formatQty(parseFloat(countTotal)) + '</th>';
        tfoot += '<th class="text-right">'+formatMoney(total_pay_bill)+'</th>';
        tfoot += '<th class="text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th>';
        tfoot += '</tr>';
        $('#slTable tfoot').html(tfoot);
        $('#tgrand_total').html(formatMoney(total_pay_bill));
        $('#total').html(formatMoney(countTotal));
        $('#titems').html(count-1);
        $('#total_items').val(count-1);
        set_page_focus();
    }
}

/* -----------------------------
 * Add Purchase Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
function add_tonewitem_item(item) {

    if (count == 1) {
        qaitems = {};
    }
    if (item == null)
        return;
   // alert(item.reference_no);
    var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
    if (qaitems[item_id]) {
    	//alert(item_id);
    	bootbox.alert("มีการเพิ่มรายการนี้แล้ว");
    } else {
        qaitems[item_id] = item;
    }
    qaitems[item_id].order = new Date().getTime();
    localStorage.setItem('qaitems', JSON.stringify(qaitems));
    loadItems();
    return true;
}

if (typeof (Storage) === "undefined") {
    $(window).bind('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}

//hellper function for customer if no localStorage value
function nsCustomer() {
    $('#slcustomer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "customers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
              //  alert(data.results);
                if (typeof data.results !== 'undefined') {
                    return {results: data.results};
                } else {
                    return {results: [{id: '', text: 'No Match Found'}]};
                }
            }
        }
    });
}