$(document).ready(function () {
    if (!localStorage.getItem('qaref')) {
        localStorage.setItem('qaref', '');
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
    if (localStorage.getItem('promotionitems')) {
        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('promotionitems')) {
                    localStorage.removeItem('promotionitems');
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

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

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
        delete promotionitems[item_id];
        row.remove();
        if(promotionitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('promotionitems', JSON.stringify(promotionitems));
            loadItems();
            return;
        }
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */
    var old_row_qty;
    $(document).on("change", '.quantity_buy', function () {
        var row = $(this).closest('tr');
       // old_row_qty =$(this).val();
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        promotionitems[item_id].row.quantity_buy = new_qty;
        localStorage.setItem('promotionitems', JSON.stringify(promotionitems));
        loadItems();
    });

    $(document).on("change", '.reduce_bath', function () {
        var row = $(this).closest('tr');
        // old_row_qty =$(this).val();
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var new_qty = parseFloat($(this).val()),
            item_id = row.attr('data-item-id');
        promotionitems[item_id].row.reduce_bath = new_qty;
        localStorage.setItem('promotionitems', JSON.stringify(promotionitems));
        loadItems();
    });

    $(document).on("change", '.reduce_percent', function () {
        var row = $(this).closest('tr');
        // old_row_qty =$(this).val();
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var new_qty = parseFloat($(this).val()),
            item_id = row.attr('data-item-id');
        promotionitems[item_id].row.reduce_percent = new_qty;
        localStorage.setItem('promotionitems', JSON.stringify(promotionitems));
        loadItems();
    });

    $(document).on("change", '#pro_type', function () {
        loadItems();
    });
    $(document).on("change", '.prod_free', function () {
        var row = $(this).closest('tr');
        // old_row_qty =$(this).val();
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var new_qty = parseFloat($(this).val()),
            item_id = row.attr('data-item-id');
        promotionitems[item_id].row.prod_free = new_qty;
        localStorage.setItem('promotionitems', JSON.stringify(promotionitems));
        loadItems();
    });

});


/* -----------------------
 * Load Items to table
 ----------------------- */

function loadItems() {
    var count_reduce_percent=0;
    var count_reduce_bath=0;
    var count_quantity_buy=0;
    var count_prod_free_buy=0;
    if (localStorage.getItem('promotionitems')) {
        count = 1;
        an = 1;

        var pro_type = $("#pro_type").val();
        $("#promotionTable tbody").empty();
        promotionitems = JSON.parse(localStorage.getItem('promotionitems'));
        sortedItems = (site.settings.item_addition == 1) ? _.sortBy(promotionitems, function(o){return [parseInt(o.order)];}) : promotionitems;
        $.each(sortedItems, function () {
            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
            item.order = item.order ? item.order : new Date().getTime();
            var product_id = item.row.id, item_qty = item.row.qty, item_option = item.row.option, item_code = item.row.code, item_serial = item.row.serial, item_name = item.row.name;
            var type = item.row.type ? item.row.type : '';
            var unit_id =  item.row.unit_id,unit =  item.row.unit, quantity_buy=item.row.prod_qty,price = item.row.price;
            var prod_total_qty = item.row.prod_total_qty, reduce_bath = item.row.reduce_bath;
            var reduce_percent = item.row.reduce_percent, prod_free = item.row.prod_free;
            var prod_type = item.row.prod_type, prod_dd = item.row.prod_dd;



            var row_no = (new Date).getTime();
            var disBath ="";
            var disPer ="";
            var disFree ="";
            if(pro_type=='0' || pro_type=='1'){
                disBath ="disabled=\"true\"";
                disPer ="disabled=\"true\"";
                disFree ="";
                reduce_bath =0;
                reduce_percent =0;
                prod_free =prod_free;
            }else{
                disBath ="";
                disPer ="";
                disFree ="disabled=\"true\"";
                reduce_bath =reduce_bath;
                reduce_percent =reduce_percent;
                prod_free =0;
            }

            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>'); //รหัสรายการสินคา้
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - ' + item_name +'</span></td>'; //รหัสรายการสินคา้
            tr_html += '<td class="text-right"><span class="sname" id="price_' + row_no + '">' + formatMoney(price) +'</span></td>'; //ราคาขาย
            tr_html += '<td><input type="text" name="quantity_buy[]" class="form-control select quantity_buy  text-right" tabindex="'+ (an+1)+'" value="'+formatQuantity2(quantity_buy)+'"/></td>';// จำนวนที่ต้องซื้อ
            tr_html += '<td><input name="unit_id[]" type="hidden" class="rid" value="' + unit_id + '">'+unit+'</td>'; //หน่วยนับ
            tr_html += '<td><input '+disBath+'  type="text" name="reduce_bath[]" class="form-control select reduce_bath text-right" tabindex="'+(an+2)+'" value="'+formatQuantity2(reduce_bath)+'"/></td>';//รายการลดเป็นบาท
            tr_html += '<td><input '+disPer+' type="text" name="reduce_percent[]" class="form-control reduce_percent text-center" tabindex="'+(an+3)+'"  value="' + formatQuantity2(reduce_percent) + '" data-id="' + reduce_percent + '" data-item="' + reduce_percent + '" id="reduce_percent_' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td><input '+disFree+' type="text" name="prod_free[]" class="form-control select prod_free text-right" tabindex="'+(an+4)+'" value="'+formatQuantity2(prod_free)+'"/></td>';//รายการลดเป็นบาท

            if (site.settings.product_serial == 1) {
                tr_html += '<td class="text-right"><input class="form-control input-sm rserial" name="serial[]" type="text" id="serial_' + row_no + '" value="'+item_serial+'"></td>';
            }
            tr_html += '<td class="text-center"><i class="fa fa-times tip qadel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);

            newTr.appendTo("#promotionTable");
           // tr_From.prependTo("#qaTable");

            count_reduce_percent += parseFloat(reduce_percent);
            count_reduce_bath += parseFloat(reduce_bath);
            count_quantity_buy += parseFloat(quantity_buy);
            count_prod_free_buy += parseFloat(prod_free);
            an= an+5    ;
            count =count+1;
        });

        var col = 2;
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-center">' + formatQty(parseFloat(count_quantity_buy) ) + '</th>';
        if (site.settings.product_serial == 1) { tfoot += '<th></th>'; }
        tfoot += '<th class="text-center"></th>';
        tfoot += '<th class="text-center">' + formatQty(parseFloat(count_reduce_bath) ) + '</th>';
        tfoot += '<th class="text-center">'+formatQty(parseFloat(count_reduce_percent) )+'</th>';
        tfoot += '<th class="text-center">'+formatQty(parseFloat(count_prod_free_buy) )+'</th>';
        tfoot += '<th class="text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th>';
        tfoot += '</tr>';
        $('#promotionTable tfoot').html(tfoot);
        //$('select.select').select2({minimumResultsForSearch: 7});

        set_page_focus();
    }
}

/* -----------------------------
 * Add Purchase Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
function add_promotion_item(item) {
    //console.log(qaitems);
    if (count == 1) {
        promotionitems = {};
    }
    //console.log(qaitems);
    if (item == null)
        return;

    var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
    //alert(item_id);
    if (promotionitems[item_id]) {
     //   alert(1);
        var new_qty = parseFloat(promotionitems[item_id].row.qty) + 1;
        promotionitems[item_id].row.base_quantity = new_qty;
        if(promotionitems[item_id].row.unit != promotionitems[item_id].row.base_unit) {
            $.each(promotionitems[item_id].units, function(){
                if (this.id == promotionitems[item_id].row.unit) {
                    promotionitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        promotionitems[item_id].row.qty = new_qty;


    } else {
       // alert(2);
        promotionitems[item_id] = item;
    }
    //alert(qaitems[item_id]);
     //   console.log(qaitems);
    promotionitems[item_id].order = new Date().getTime();
    localStorage.setItem('promotionitems', JSON.stringify(promotionitems));
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