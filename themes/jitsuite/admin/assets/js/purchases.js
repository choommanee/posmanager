$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}
// Order level shipping and discoutn localStorage
if (podiscount = localStorage.getItem('podiscount')) {
    $('#podiscount').val(podiscount);
}
$('#potax2').change(function (e) {
    localStorage.setItem('potax2', $(this).val());
});
if (potax2 = localStorage.getItem('potax2')) {
    $('#potax2').select2("val", potax2);
}
$('#postatus').change(function (e) {
    localStorage.setItem('postatus', $(this).val());
});
if (postatus = localStorage.getItem('postatus')) {
    $('#postatus').select2("val", postatus);
}
var old_shipping;
$('#poshipping').focus(function () {
    old_shipping = $(this).val();
}).change(function () {
    var posh = $(this).val() ? $(this).val() : 0;
    if (!is_numeric(posh)) {
        $(this).val(old_shipping);
        bootbox.alert(lang.unexpected_value);
        return;
    }
    //alert(invoice_tax);
    shipping = parseFloat(posh);
    var shippingType= $('#shipping_pay_type').val();
    if (site.settings.tax2 != 0) {
        if (potax2 = localStorage.getItem('potax2')) {
            $.each(tax_rates, function () {
                if (this.id == potax2) {
                    if (this.type == 2) {
                        invoice_tax = formatDecimal(this.rate);
                    }
                    if (this.type == 1) {

                        if(shippingType=="1"){
                            invoice_tax = formatDecimal((((total+shipping - order_discount) * this.rate) / 100), 4);
                        }else if(shippingType=="2"){
                            invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 4);
                        }
                    }
                }
            });
        }
    }
    //alert("shippingType:"+shippingType+"invoice_tax"+invoice_tax+";total="+total+";shipping:"+shipping+"order_discount:"+order_discount);
    localStorage.setItem('poshipping', shipping);
    var gtotal = ((total + invoice_tax) - order_discount) + shipping;
    $('#gtotal').text(formatMoney(gtotal));
    $('#tship').text(formatMoney(shipping));
    $('#ttax2').text(formatMoney(invoice_tax));

});
if (poshipping = localStorage.getItem('poshipping')) {
    shipping = parseFloat(poshipping);
    $('#poshipping').val(shipping);
}

$('#popayment_term').change(function (e) {
    localStorage.setItem('popayment_term', $(this).val());
});
if (popayment_term = localStorage.getItem('popayment_term')) {
    $('#popayment_term').val(popayment_term);
}

// If there is any item in localStorage
if (localStorage.getItem('poitems')) {
    loadItems();
}

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('poitems')) {
                    localStorage.removeItem('poitems');
                }
                if (localStorage.getItem('podiscount')) {
                    localStorage.removeItem('podiscount');
                }
                if (localStorage.getItem('potax2')) {
                    localStorage.removeItem('potax2');
                }
                if (localStorage.getItem('poshipping')) {
                    localStorage.removeItem('poshipping');
                }
                if (localStorage.getItem('poref')) {
                    localStorage.removeItem('poref');
                }
                if (localStorage.getItem('powarehouse')) {
                    localStorage.removeItem('powarehouse');
                }
                if (localStorage.getItem('ponote')) {
                    localStorage.removeItem('ponote');
                }
                if (localStorage.getItem('posupplier')) {
                    localStorage.removeItem('posupplier');
                }
                if (localStorage.getItem('pocurrency')) {
                    localStorage.removeItem('pocurrency');
                }
                if (localStorage.getItem('poextras')) {
                    localStorage.removeItem('poextras');
                }
                if (localStorage.getItem('potype_vat')) {
                    localStorage.removeItem('potype_vat');
                }
                if (localStorage.getItem('podate')) {
                    localStorage.removeItem('podate');
                }
                if (localStorage.getItem('postatus')) {
                    localStorage.removeItem('postatus');
                }
                if (localStorage.getItem('popayment_term')) {
                    localStorage.removeItem('popayment_term');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

// save and load the fields in and/or from localStorage
var $supplier = $('#posupplier'), $currency = $('#pocurrency');

$('#poref').change(function (e) {
    localStorage.setItem('poref', $(this).val());
});
if (poref = localStorage.getItem('poref')) {
    $('#poref').val(poref);
}
$('#powarehouse').change(function (e) {
    localStorage.setItem('powarehouse', $(this).val());
});
if (powarehouse = localStorage.getItem('powarehouse')) {
    $('#powarehouse').select2("val", powarehouse);
}

        $('#ponote').redactor('destroy');
        $('#ponote').redactor({
            buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
            formattingTags: ['p', 'pre', 'h3', 'h4'],
            minHeight: 100,
            changeCallback: function (e) {
                var v = this.get();
                localStorage.setItem('ponote', v);
            }
        });
        if (ponote = localStorage.getItem('ponote')) {
            $('#ponote').redactor('set', ponote);
        }
        $supplier.change(function (e) {
            localStorage.setItem('posupplier', $(this).val());
            $('#supplier_id').val($(this).val());
        });
        if (posupplier = localStorage.getItem('posupplier')) {
            $supplier.val(posupplier).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url+"suppliers/getSupplier/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + "suppliers/suggestions",
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });

} else {
    nsSupplier();
}

    /*$('.rexpiry').change(function (e) {
        var item_id = $(this).closest('tr').attr('data-item-id');
        poitems[item_id].row.expiry = $(this).val();
        localStorage.setItem('poitems', JSON.stringify(poitems));
    });*/
if (localStorage.getItem('poextras')) {
    $('#extras').iCheck('check');
    $('#extras-con').show();
}
$('#extras').on('ifChecked', function () {
    localStorage.setItem('poextras', 1);
    $('#extras-con').slideDown();
});
$('#extras').on('ifUnchecked', function () {
    localStorage.removeItem("poextras");
    $('#extras-con').slideUp();
});
$('#type_vat').on('ifChecked', function () {
    localStorage.setItem('potype_vat', 1);
    loadItems();
});
$('#type_vat').on('ifUnchecked', function () {
    localStorage.removeItem("potype_vat");
    loadItems();
});

$(document).on('change', '.rexpiry', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    poitems[item_id].row.expiry = $(this).val();
    localStorage.setItem('poitems', JSON.stringify(poitems));
});


// prevent default action upon enter
$('body').bind('keypress', function (e) {
    if ($(e.target).hasClass('redactor_editor')) {
        return true;
    }
    if (e.keyCode == 13) {
       // alert(e.id);
        var str = $(e.target).attr("id");
       // alert(str);
        str = " "+String(str);
       // str = str.replace(" ","");
        var n = str.search("cost_");

        if(n > 0){
            str = str.replace(" ","");
            var indexd= $(e.target).attr('tabindex');
            var res = str.replace("cost_", "quantity_");
            var nextIndex = parseInt(indexd)+1;
            $('[tabindex='+(nextIndex)+']').focus().select();
          //  alert(res);
        }else{
            //alert(str);
            var n = str.search("quantity_");

            if(n > 0){
                str = str.replace(" ","");
                var indexd= $(e.target).attr('tabindex');
                var nextIndex = parseInt(indexd)+1;
                $('[tabindex='+(nextIndex)+']').focus().select();
               // alert(res);
            }else{
                var n = str.search("discount_");
                if(n > 0){
                    //str = str.replace(" ","");
                    var indexd= $(e.target).attr('tabindex');
                    var nextIndex = parseInt(indexd)+1;
                    $('[tabindex='+(nextIndex)+']').focus().select();
                    // alert(res);
                }
            }
        }

        e.preventDefault();
        return false;
    }
});

// Order tax calcuation
if (site.settings.tax2 != 0) {
    $('#potax2').change(function () {
        localStorage.setItem('potax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calcuation
var old_podiscount;
$('#podiscount').focus(function () {
    old_podiscount = $(this).val();
}).change(function () {
    var pod = $(this).val() ? $(this).val() : 0;
    if (is_valid_discount(pod)) {
        localStorage.removeItem('podiscount');
        localStorage.setItem('podiscount', pod);
        loadItems();
        return;
    } else {
        $(this).val(old_podiscount);
        bootbox.alert(lang.unexpected_value);
        return;
    }

});


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

     $(document).on('click', '.podel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete poitems[item_id];
        row.remove();
        if(poitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('poitems', JSON.stringify(poitems));
            loadItems();
            return;
        }
    });

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
     $(document).on('click', '.edit', function () {
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = poitems[item_id];
        var qty = row.children().children('.rquantity').val(),
        product_option = row.children().children('.roption').val(),
        unit_cost = formatDecimal(row.children().children('.rucost').val()),
        discount = row.children().children('.rdiscount').val();
        $('#prModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        var real_unit_cost = item.row.real_unit_cost;
        var net_cost = real_unit_cost;
        if (site.settings.tax1) {
            $('#ptax').select2('val', item.row.tax_rate);
            $('#old_tax').val(item.row.tax_rate);
            var item_discount = 0, ds = discount ? discount : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = parseFloat(((real_unit_cost) * parseFloat(pds[0])) / 100);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                item_discount = parseFloat(ds);
            }
            net_cost -= item_discount;
            var pr_tax = item.row.tax_rate, pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                $.each(tax_rates, function () {
                    if(this.id == pr_tax){
                        if (this.type == 1) {

                            if (poitems[item_id].row.tax_method == 0) {
                                pr_tax_val = formatDecimal((((real_unit_cost-item_discount) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                                net_cost -= pr_tax_val;
                            } else {
                                pr_tax_val = formatDecimal((((real_unit_cost-item_discount) * parseFloat(this.rate)) / 100), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                            }

                        } else if (this.type == 2) {

                            pr_tax_val = parseFloat(this.rate);
                            pr_tax_rate = this.rate;

                        }
                    }
                });
            }
        }
        if (site.settings.product_serial !== 0) {
            $('#pserial').val(row.children().children('.rserial').val());
        }
        var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if(item.options !== false) {
            var o = 1;
            opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
            $.each(item.options, function () {
                if(o == 1) {
                    if(product_option == '') { product_variant = this.id; } else { product_variant = product_option; }
                }
                $("<option />", {value: this.id, text: this.name}).appendTo(opt);
                o++;
            });
        }

        uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
        $.each(item.units, function () {
            if(this.id == item.row.unit) {
                $("<option />", {value: this.id, text: this.name, selected:true}).appendTo(uopt);
            } else {
                $("<option />", {value: this.id, text: this.name}).appendTo(uopt);
            }
        });

        $('#poptions-div').html(opt);
        $('#punits-div').html(uopt);
        $('select.select').select2({minimumResultsForSearch: 7});
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pcost').val(unit_cost);
        $('#punit_cost').val(formatDecimal(parseFloat(unit_cost)+parseFloat(pr_tax_val)));
        $('#poption').select2('val', item.row.option);
        $('#old_cost').val(unit_cost);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pexpiry').val(row.children().children('.rexpiry').val());
        $('#pdiscount').val(discount);
        $('#net_cost').text(formatMoney(net_cost));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#psubtotal').val('');
        $('#prModal').appendTo("body").modal('show');

    });

    $('#prModal').on('shown.bs.modal', function (e) {
        if($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pcost, #ptax, #pdiscount', function () {
       // alert("tst");
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_cost = parseFloat($('#pcost').val());
        var item = poitems[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_cost) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_cost -= item_discount;
        var pr_tax = $('#ptax').val(), item_tax_method = item.row.tax_method;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function () {
                if(this.id == pr_tax){
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_cost -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }

        $('#net_cost').text(formatMoney(unit_cost));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = poitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        if(unit != poitems[item_id].row.base_unit) {
            $.each(item.units, function() {
                if (this.id == unit) {
                    $('#pcost').val(formatDecimal((parseFloat(item.row.base_unit_cost)*(unitToBaseQty(1, this))), 4)).change();
                }
            });
        } else {
            $('#pcost').val(formatDecimal(item.row.base_unit_cost)).change();
        }
    });

    $(document).on('click', '#calculate_unit_price', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = poitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var subtotal = parseFloat($('#psubtotal').val()),
        qty = parseFloat($('#pquantity').val());
        $('#pcost').val(formatDecimal((subtotal/qty), 4)).change();
        return false;
    });

    /* -----------------------
     * Edit Row Method
     ----------------------- */
     $(document).on('click', '#editItem', function () {
       //  alert("teset");
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'), new_pr_tax = $('#ptax').val(), new_pr_tax_rate = {};
        if (new_pr_tax) {
            $.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }

        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if(unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        poitems[item_id].row.fup = 1,
        poitems[item_id].row.qty = parseFloat($('#pquantity').val()),
        poitems[item_id].row.base_quantity = parseFloat(base_quantity),
        poitems[item_id].row.unit = unit,
        poitems[item_id].row.real_unit_cost = parseFloat($('#pcost').val()),
        poitems[item_id].row.tax_rate = new_pr_tax,
        poitems[item_id].tax_rate = new_pr_tax_rate,
        poitems[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '0',
        poitems[item_id].row.option = $('#poption').val(),
        poitems[item_id].row.expiry = $('#pexpiry').val() ? $('#pexpiry').val() : '';
        localStorage.setItem('poitems', JSON.stringify(poitems));
        $('#prModal').modal('hide');
        loadItems();
        return;
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
     $(document).on('click', '#addManually', function (e) {
        $('#mModal').appendTo("body").modal('show');
        return false;
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */
     var old_row_qty;
     $(document).on("focus", '.rquantity', function () {
        old_row_qty = $(this).val();
    }).on("change", '.rquantity', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        poitems[item_id].row.base_quantity = new_qty;
        if(poitems[item_id].row.unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == poitems[item_id].row.unit) {
                    poitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        poitems[item_id].row.qty = new_qty;
        poitems[item_id].row.received = new_qty;
        poitems[item_id].row.subtotal = (new_qty*poitems[item_id].row.cost);
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    var old_received;
     $(document).on("focus", '.received', function () {
        old_received = $(this).val();
    }).on("change", '.received', function () {
        var row = $(this).closest('tr');
        new_received = $(this).val() ? $(this).val() : 0;
        if (!is_numeric(new_received)) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_received = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        if (new_received > poitems[item_id].row.qty) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        unit = formatDecimal(row.children().children('.runit').val()),
        $.each(poitems[item_id].units, function(){
            if (this.id == unit) {
                qty_received = formatDecimal(unitToBaseQty(new_received, this), 4);
            }
        });
        poitems[item_id].row.unit_received = new_received;
        poitems[item_id].row.received = qty_received;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    /* --------------------------
    * Edit Row Cost Method
    -------------------------- */
    var old_subtotal;
    $(document).on("focus", '.ssubtotal', function () {
        old_subtotal = $(this).val();
    }).on("change", '.ssubtotal', function () {
        var row = $(this).closest('tr');
        var CheckSub = parseFloat($(this).val().replace(",",""));
       // alert(CheckSub);
        if (!is_numeric(CheckSub)) {
            $(this).val(old_subtotal);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var new_subtotal = parseFloat(CheckSub),
            item_id = row.attr('data-item-id');

        poitems[item_id].row.subtotal = new_subtotal;
        // alert(new_cost);
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });
    /* --------------------------
     * Edit Row Cost Method
     -------------------------- */
     var old_cost;
     $(document).on("focus", '.rcost', function () {
        old_cost = $(this).val();
    }).on("change", '.rcost', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_cost);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var new_cost = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');

        poitems[item_id].row.cost = new_cost;

        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });
    /* --------------------------
    * Edit Row Discount Method
    -------------------------- */
    var old_discount;
    $(document).on("focus", '.rdiscount', function () {
        old_discount = $(this).val();
    }).on("change", '.rdiscount', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_discount);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var new_discount = parseFloat($(this).val()),
            item_id = row.attr('data-item-id');

        poitems[item_id].row.discount = new_discount;
        // alert(new_cost);
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
     $('#posupplier').select2('readonly', false);
     return false;
 });

    if (po_edit) {
        $('#posupplier').select2("readonly", true);
    }

});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for supplier if no localStorage value
function nsSupplier() {
    $('#posupplier').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "suppliers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {results: data.results};
                } else {
                    return {results: [{id: '', text: 'No Match Found'}]};
                }
            }
        }
    });
}

function loadItems() {
    //alert("test");
    if (localStorage.getItem('poitems')) {
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#poTable tbody").empty();
        poitems = JSON.parse(localStorage.getItem('poitems'));
        sortedItems = (site.settings.item_addition == 1) ? _.sortBy(poitems, function(o){return [parseInt(o.order)];}) : poitems;

        var order_no = new Date().getTime();
        $.each(sortedItems, function () {
            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
            item.order = item.order ? item.order : order_no++;
            var product_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_cost = item.row.cost, item_oqty = item.row.oqty, item_qty = item.row.qty, item_bqty = item.row.quantity_balance, item_expiry = item.row.expiry, item_tax_method = item.row.tax_method, item_ds = item.row.discount, item_discount = 0, item_option = item.row.option, item_code = item.row.code, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
            var qty_received = (item.row.received >= 0) ? item.row.received : item.row.qty;
            var item_supplier_part_no = item.row.supplier_part_no ? item.row.supplier_part_no : '';
            if (item.row.new_entry == 1) { item_bqty = item_qty; item_oqty = item_qty; }
            var unit_cost = item.row.cost;
           // alert(item.row.cost);
            var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
            var supplier = localStorage.getItem('posupplier'), belong = false;
            var subtotal =item.row.subtotal;

                if (supplier == item.row.supplier1) {
                    belong = true;
                } else
                if (supplier == item.row.supplier2) {
                    belong = true;
                } else
                if (supplier == item.row.supplier3) {
                    belong = true;
                } else
                if (supplier == item.row.supplier4) {
                    belong = true;
                } else
                if (supplier == item.row.supplier5) {
                    belong = true;
                }
                var unit_qty_received = qty_received;
                if(item.row.fup != 1 && product_unit != item.row.base_unit) {
                    $.each(item.units, function(){
                        if (this.id == product_unit) {
                            base_quantity = formatDecimal(unitToBaseQty(item.row.qty, this), 4);
                            unit_qty_received = item.row.unit_received ? item.row.unit_received : formatDecimal(baseToUnitQty(qty_received, this), 4);
                            unit_cost = formatDecimal((parseFloat(item.row.base_unit_cost)*(unitToBaseQty(1, this))), 4);
                        }
                    });
                }
                var ds = item_ds ? item_ds : '0';
                //alert(ds+""+unit_cost);
                item_discount = calculateDiscount(ds, unit_cost);
                product_discount += parseFloat(item_discount * item_qty);

                unit_cost = formatDecimal(unit_cost,3);
                var pr_tax = item.tax_rate;
                var pr_tax_val = pr_tax_rate = 0;
               // if (site.settings.tax1 == 1 && (ptax = calculateTax(pr_tax, unit_cost, item_tax_method))) {
               //     pr_tax_val = ptax[0];
               //     pr_tax_rate = ptax[1];
               //     product_tax += pr_tax_val * item_qty;
               // }

                //var type_vat = $('#type_vat').val();
                if(localStorage.getItem('potype_vat')=='1'){

                        item_cost = formatDecimals(((unit_cost)-(unit_cost*7)/107),3);
                        unit_cost = formatDecimals(((unit_cost)-(unit_cost*7)/107),3);



                    //alert(item_cost);
                }else{


                        item_cost = item_tax_method == 0 ? formatDecimal(unit_cost-pr_tax_val, 3) : formatDecimal(unit_cost,3);
                        unit_cost = formatDecimal(unit_cost-item_discount, 3);
                    //}

                }
               // alert(unit_cost);

                var ssubtotal = formatDecimal((item_cost-item_discount)*qty_received,3);
                var sel_opt = '';
                $.each(item.options, function () {
                    if(this.id == item_option) {
                        sel_opt = this.name;
                    }
                });

            var row_no = (new Date).getTime();
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><input name="part_no[]" type="hidden" class="rpart_no" value="' + item_supplier_part_no + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+' <span class="label label-default">'+item_supplier_part_no+'</span></span> <i class="pull-right fa fa-edit tip edit" id="' + row_no + '" data-item="' + item_id + '" title="Edit" style="cursor:pointer;"></i></td>';
            if (site.settings.product_expiry == 1) {
                tr_html += '<td><input class="form-control date rexpiry" name="expiry[]" type="text" value="' + item_expiry + '" data-id="' + row_no + '" data-item="' + item_id + '" id="expiry_' + row_no + '"></td>';
            }
            tr_html += '<td class="text-right"><input class="form-control input-sm text-right rcost" name="net_cost[]" type="text" id="cost_' + row_no + '" value="' + item_cost + '" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'"><input class="rucost" name="unit_cost[]" type="hidden" value="' + unit_cost + '"><input class="realucost" name="real_unit_cost[]" type="hidden" value="' + item.row.real_unit_cost + '"><span class="text-right scost" id="scost_' + row_no + '" style="display: none;">' + formatMoney(item.row.real_unit_cost,1) + '</span></td>';
            tr_html += '<td><input name="quantity_balance[]" type="hidden" class="rbqty" value="' + item_bqty + '"><input class="form-control text-center rquantity" name="quantity[]" type="text" tabindex="'+((site.settings.set_focus == 1) ? an : (an+2))+'" value="' + formatDecimal(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"><input name="product_unit[]" type="hidden" class="runit" value="' + product_unit + '"><input name="product_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';
            if (po_edit) {
                tr_html += '<td class="rec_con"><input name="ordered_quantity[]" type="hidden" class="oqty" value="' + item_oqty + '"><input class="form-control text-center received" name="received[]" type="text" value="' + formatDecimal(unit_qty_received) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="received_' + row_no + '" onClick="this.select();"><input name="received_base_quantity[]" type="hidden" class="rrbase_quantity" value="' + qty_received + '"></td>';
            }
            if (site.settings.product_discount == 1) {
                tr_html += '<td class="text-right"><input class="form-control input-sm rdiscount" name="product_discount[]" type="text" id="discount_' + row_no + '" value="' + item_ds + '" tabindex="'+((site.settings.set_focus == 1) ? an : (an+3))+'"><span class="text-right sdiscount text-danger" id="sdiscount_' + row_no + '"></span></td>';
            }

           // if(item.row.subtotal > 0){
                tr_html += '<td class="text-right"><input class="form-control input-sm text-right ssubtotal" name="subtotal[]" type="text" id="subtotal_' + row_no + '" value="' + formatMoney2(ssubtotal,3) + '"></td>';
            //}else{
            //    tr_html += '<td class="text-right"><input class="form-control input-sm text-right ssubtotal" name="subtotal[]" type="text" id="subtotal_' + row_no + '" value="' + formatMoney2(((parseFloat(item_cost)-parseFloat(item_discount)) * parseFloat(item_qty)),3) + '"></td>';
            //}




            tr_html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.appendTo("#poTable");

                total += parseFloat(item.row.subtotal);
            //}

            count += parseFloat(item_qty);
            an= an+3;
            if(!belong)
                $('#row_' + row_no).addClass('warning');
        });

        var col = 2;
        if (site.settings.product_expiry == 1) { col++; }
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-center">' + formatQty(parseFloat(count) - 1) + '</th>';
        if (po_edit) {
            tfoot += '<th class="rec_con"></th>';
        }
        //alert(total);
        if (site.settings.product_discount == 1) {
            tfoot += '<th class="text-right">'+formatMoney2(product_discount,2)+'</th>';
        }

        tfoot += '<th class="text-right">'+formatMoney2(total,2)+'</th><th class="text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>';
        $('#poTable tfoot').html(tfoot);

        // Order level discount calculations
        if (podiscount = localStorage.getItem('podiscount')) {
            var ds = podiscount;
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    order_discount = formatDecimal(((total * parseFloat(pds[0])) / 100), 4);
                } else {
                    order_discount = formatDecimal(ds);
                }
            } else {
                order_discount = formatDecimal(ds);
            }
        }
        var shippingType= $('#shipping_pay_type').val();

        // Order level tax calculations
        total_discount = parseFloat(order_discount + product_discount);
        if (site.settings.tax2 != 0) {
            if (potax2 = localStorage.getItem('potax2')) {
                $.each(tax_rates, function () {
                    if (this.id == potax2) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {

                            if(shippingType=="1"){
                                invoice_tax = formatDecimal((((total+shipping - order_discount) * this.rate) / 100), 4);
                            }else if(shippingType=="2"){
                                invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 4);
                            }
                        }
                    }
                });
            }
        }

        // Totals calculations after item addition
        //alert(invoice_tax);
        var gtotal = ((total + invoice_tax) - order_discount)+ shipping;
        //var gtotal = ((total + invoice_tax) - order_discount)+ shipping;

        $('#tship').text(formatMoney2(shipping,2));
        $('#total').text(formatMoney2(total,2));
        $('#titems').text((an-1)+' ('+(formatDecimal(parseFloat(count) - 1))+')');
        $('#tds').text(formatMoney2(order_discount,2));
        if (site.settings.tax1) {
            $('#ttax1').text(formatMoney(product_tax));
        }
        if (site.settings.tax2 != 0) {
            $('#ttax2').text(formatMoney(invoice_tax));
        }
        $('#gtotal').text(formatMoney2(gtotal,2));
        if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
            $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
            $(window).scrollTop($(window).scrollTop() + 1);
        }
        set_page_focus();
    }
}

/* -----------------------------
 * Add Purchase Iten Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_purchase_item(item) {
    if (count == 1) {
        poitems = {};
        if ($('#posupplier').val()) {
            $('#posupplier').select2("readonly", true);
        } else {
            bootbox.alert(lang.select_above);
            item = null;
            return;
        }
    }
    if (item == null)
        return;

    var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
    if (poitems[item_id]) {

        var new_qty = parseFloat(poitems[item_id].row.qty) + 1;
        poitems[item_id].row.base_quantity = new_qty;
        if(poitems[item_id].row.unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == poitems[item_id].row.unit) {
                    poitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        //alert(item.row.cost);
        poitems[item_id].row.qty = new_qty;
        poitems[item_id].row.subtotal = (item.row.cost*new_qty);

    } else {
        poitems[item_id] = item;
        //alert(item.row.cost);
       // poitems[item_id].row.qty = new_qty;
        poitems[item_id].row.subtotal = (item.row.cost*1);
    }
    poitems[item_id].order = new Date().getTime();
    localStorage.setItem('poitems', JSON.stringify(poitems));
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
