/* Sximo builder 
	copyright 2014 . sximo builder com 
*/

jQuery(document).ready(function($){
//alert('test');
	$('.msg').click(function(){
		$(this).remove();						 
	});
								
		if($.cookie("sxintheme") != '')
		{
			$('#switchTheme').attr('href',$.cookie("sxintheme"));
		} 		
		if($.cookie("sximo-sidebar") =='minimize-sidemenu'){
			$("body").addClass("minimize-sidemenu");
			$('#sidemenu').removeClass('expanded-menu');
		} else {
			$("body").removeClass("minimize-sidemenu");
			$('#sidemenu').addClass('expanded-menu');
		}
		$(window).bind("load resize", function() {
			if ($(this).width() < 769) {
				$('body').addClass('body-small')
			} else {
				$('body').removeClass('body-small')
			}
		})
      /*Return to top*/
      var offset = 220;
      var duration = 500;
      var button = $('<a href="#" class="back-to-top"><i class="fa fa-angle-up"></i></a>');
      button.appendTo("body");
      
      jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.back-to-top').fadeIn(duration);
        } else {
            jQuery('.back-to-top').fadeOut(duration);
        }
      });
    
      jQuery('.back-to-top').click(function(event) {
          event.preventDefault();
          jQuery('html, body').animate({scrollTop: 0}, duration);
          return false;
      });

     
	$('.date').datetimepicker({format:'d/m/Y'});
	$('.datetime').datetimepicker({format: 'd/m/Y hh:ii:ss'}); 
	
	/* Tooltip */
	$('.previewImage').fancybox();	
	$('.tips').tooltip();
	
	$('.markItUp').summernote({
			  codemirror: { // codemirror options
				theme: 'monokai'
			  }
			});
	$(".select2").select2({ width:"98%"});	
	$(".select-liquid").select2({
		minimumResultsForSearch: "-1",
	});	
	$('.panel-trigger').click(function(e){
		e.preventDefault();
		$(this).toggleClass('active');
	});

	$('.dropdown, .btn-group').on('show.bs.dropdown', function(e){
		$(this).find('.dropdown-menu').first().stop(true, true).fadeIn(100);
	});
	$('.dropdown, .btn-group').on('hide.bs.dropdown', function(e){
		$(this).find('.dropdown-menu').first().stop(true, true).fadeOut(100);
	});
	$('.popup').click(function (e) {
		e.stopPropagation();
	});
        
     window.prettyPrint && prettyPrint();

	$(".checkall").click(function() {
		var cblist = $(".ids");
		if($(this).is(":checked"))
		{				
			cblist.prop("checked", !cblist.is(":checked"));
		} else {	
			cblist.removeAttr("checked");
		}	
	});
	
	$('.nav li ul li.active').parents('li').addClass('active');
	
	
		$('input[type="checkbox"],input[type="radio"]').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green',
		});	
		$('.checkall').on('ifChecked',function(){
			$('input[type="checkbox"]').iCheck('check');
		});
		$('.checkall').on('ifUnchecked',function(){
			$('input[type="checkbox"]').iCheck('uncheck');
		});	
    $('.navbar-minimalize').click(function () {
      var w = $("body");
		w.toggleClass("minimize-sidemenu");
			
		if( w.hasClass('minimize-sidemenu'))
		{
			$('#sidemenu').removeClass('expanded-menu');
			$.cookie("sximo-sidebar",'minimize-sidemenu', {expires: 365, path: '/'});
		} else {
			$('#sidemenu').addClass('expanded-menu');
			 $.cookie("sximo-sidebar",'maximaze-sidemenu', {expires: 365, path: '/'});	
		}		
    })	
})
function SximoConfirmDelete( url )
{
	if(confirm('Are u sure deleting this record ? '))
	{
		window.location.href = url;	
	}
	return false;
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (elt /*, from*/) {
        var len = this.length >>> 0;
        var from = Number(arguments[1]) || 0;
        from = (from < 0) ? Math.ceil(from) : Math.floor(from);
        if (from < 0) from += len;

        for (; from < len; from++) {
            if (from in this && this[from] === elt) return from;
        }
        return -1;
    };
}

function SximoDelete(  )
{	
	var total = $('input[class="ids"]:checkbox:checked').length;
	if(confirm('are u sure removing selected rows ?'))
	{
			$('#SximoTable').submit();// do the rest here	
	}	
}	
function SximoModal( url , title)
{
	$('#sximo-modal-content').html(' ....Loading content , please wait ...');
	$('.modal-title').html(title);
	//alert(url);
	$('#sximo-modal-content').load(url,function(){});
	$('#sximo-modal').modal('show');	
}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function chkNum(ele)
{
	var num = parseFloat(ele.value);
	ele.value = addCommas(num.toFixed(2));
}

function autoTab2(obj,typeCheck){
			if(typeCheck==1){
				var pattern=new String("_-____-_____-_-__"); // กำหนดรูปแบบในนี้
				var pattern_ex=new String("-"); // กำหนดสัญลักษณ์หรือเครื่องหมายที่ใช้แบ่งในนี้		
			}else{
				var pattern=new String("__-____-____"); // กำหนดรูปแบบในนี้
				var pattern_ex=new String("-"); // กำหนดสัญลักษณ์หรือเครื่องหมายที่ใช้แบ่งในนี้					
			}
			var returnText=new String("");
			var obj_l=obj.value.length;
			var obj_l2=obj_l-1;
			for(i=0;i<pattern.length;i++){			
				if(obj_l2==i && pattern.charAt(i+1)==pattern_ex){
					returnText+=obj.value+pattern_ex;
					obj.value=returnText;
				}
			}
			if(obj_l>=pattern.length){
				obj.value=obj.value.substr(0,pattern.length);			
			}
	}


	function validateNumber(event) {
	    var key = window.event ? event.keyCode : event.which;
	
	    if (event.keyCode === 8 || event.keyCode === 46
	        || event.keyCode === 37 || event.keyCode === 39) {
	        return true;
	    }
	    else if ( key < 48 || key > 57 ) {
	        return false;
	    }
	    else return true;
	}
	
	function Popwin(urldata,title,optionwin) {
		//alert(urldata);
		 window.open(urldata,"","width=1000,height=800");
	}
	
	function Popwin2(urldata,title,optionwin) {
		window.open(urldata,"","width=1000,height=800");
	}
        
        function addFormat(obj){
            $(obj).val(number_format($(obj).val(),2,'.',','));
        }
	
	function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
	}
	
	function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Drew Noakes
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

;(function ($, window, document, undefined) {

    var pluginName = "sximMenu",
        defaults = {
            toggle: true
        };

    function Plugin(element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype = {
        init: function () {

            var $this = $(this.element),
                $toggle = this.settings.toggle;

            $this.find('li.active').has('ul').children('ul').addClass('collapse in');
            $this.find('li').not('.active').has('ul').children('ul').addClass('collapse');

            $this.find('li').has('ul').children('a').on('click', function (e) {
                e.preventDefault();

                $(this).parent('li').toggleClass('active').children('ul').collapse('toggle');

                if ($toggle) {
                    $(this).parent('li').siblings().removeClass('active').children('ul.in').collapse('hide');
                }
            });
        }
    };

    $.fn[ pluginName ] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };

})(jQuery, window, document);
