<?php 
error_reporting(0);	
//include_once ('/config/config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=tis-620" />
<title>file manager</title>
<link rel="stylesheet" type="text/css" href="javascript/jquery/ui/themes/ui-lightness/ui.all.css" />
<script type="text/javascript" src="javascript/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="javascript/jquery/ui/ui.core.js"></script>
<script type="text/javascript" src="javascript/jquery/jstree/jquery.tree.min.js"></script>
<script type="text/javascript" src="javascript/jquery/ajaxupload.js"></script>
<script type="text/javascript" src="javascript/jquery/ui/ui.draggable.js"></script>
<script type="text/javascript" src="javascript/jquery/ui/ui.resizable.js"></script>
<script type="text/javascript" src="javascript/jquery/ui/ui.dialog.js"></script>
<script type="text/javascript" src="javascript/jquery/ui/external/bgiframe/jquery.bgiframe.js"></script>
<style type="text/css">
body {
	padding: 0;
	margin: 0;
	background: #F7F7F7;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}
img {
	border: 0;
}
#container {
	padding: 0px 10px 7px 10px;
	height: 340px;
}
#menu {
	clear: both;
	height: 29px;
	margin-bottom: 3px;
}
#column_left {
	background: #FFF;
	border: 1px solid #CCC;
	float: left;
	width: 20%;
	height: 320px;
	overflow: auto;
}
#column_right {
	background: #FFF;
	border: 1px solid #CCC;
	float: right;
	width: 78%;
	height: 320px;
	overflow: auto;
	text-align: center;
}
#column_right div {
	text-align: left;
	padding: 5px;
}
#column_right a {
	display: inline-block;
	text-align: center;
	border: 1px solid #EEEEEE;
	cursor: pointer;
	margin: 5px;
	padding: 5px;
}
#column_right a.selected {
	border: 1px solid #7DA2CE;
	background: #EBF4FD;
}
#column_right input {
	display: none;
}
#dialog {
	display: none;
}
.button {
	display: block;
	float: left;
	padding: 8px 5px 8px 25px;
	margin-right: 5px;
	background-position: 5px 6px;
	background-repeat: no-repeat;
	cursor: pointer;
}
.button:hover {
	background-color: #EEEEEE;
}
.thumb {
	padding: 5px;
	width: 105px;
	height: 105px;
	background: #F7F7F7;
	border: 1px solid #CCCCCC;
	cursor: pointer;
	cursor: move;
	position: relative;
}
</style>
<link rel="stylesheet" type="text/css" media="all" href="javascript/jquery/jstree/themes/classic/style.css">
</head>
<body>
<div id="container">
  <div id="menu">
	<a id="create" class="button" style="background-image: url('image/filemanager/folder.png');">โฟลเดอร์</a>
	<a id="delete" class="button" style="background-image: url('image/filemanager/edit-delete.png');">ลบ</a>
	<a id="move" class="button" style="background-image: url('image/filemanager/edit-cut.png');">ย้าย</a>
	<a id="copy" class="button" style="background-image: url('image/filemanager/edit-copy.png');">copy</a>
	<a id="rename" class="button" style="background-image: url('image/filemanager/edit-rename.png');">rename</a>
	<a id="upload" class="button" style="background-image: url('image/filemanager/upload.png');">upload</a>
	<a id="refresh" class="button" style="background-image: url('image/filemanager/refresh.png');">refresh</a>
	</div>
  <div id="column_left"></div>
  <div id="column_right"></div>
  <div id="dialogmsg"></div>
</div>
<script type="text/javascript"><!--
$(document).ready(function () { 
	$('#column_left').tree({
		data: { 
			type: 'json',
			async: true, 
			opts: { 
				method: 'POST', 
				url: 'filemanagercontroll.php?call=dir'
			} 
		},
		selected: 'top',
		opened: [],
		ui: {		
			theme_name: 'classic',
			animation: 700
		},	
		types: { 
			'default': {
				clickable: true,
				creatable: false,
				renameable: false,
				deletable: false,
				draggable: false,
				max_children: -1,
				max_depth: -1,
				valid_children: 'all'
			}
		},
		callback: {
			beforedata: function(NODE, TREE_OBJ) { 
				if (NODE == false) {
					TREE_OBJ.settings.data.opts.static = [ 
						{
							data: 'image',
							attributes: { 
								'id': 'top',
								'directory': ''
							}, 
							state: 'open'
						}
					];
					return { 'directory': '' } 
				} else {
					TREE_OBJ.settings.data.opts.static = false;  
					return { 'directory': $(NODE).attr('directory') } 
				}
			},		
			onselect: function (NODE, TREE_OBJ) {
				$.ajax({
					url: 'filemanagercontroll.php?call=files',
					type: 'POST',
					data: 'directory=' + encodeURIComponent($(NODE).attr('directory')),
					dataType: 'json',
					success: function(json) {
						html = '<div>';
						if (json) {
							for (i = 0; i < json.length; i++) {					
								name = '';
								filename = json[i]['filename'];								
								for (j = 0; j < filename.length; j = j + 15) {
									name += filename.substr(j, 15) + '<br />';
								}
								name += json[i]['size'];
								html += '<a file="' + json[i]['file'] + '" thumb = "'+json[i]['thumb']+'" filetype="'+json[i]['filetype']+'"><img src="' + json[i]['thumb'] + '" title="' + json[i]['filename'] + '" /><br />' + name + '</a>';
							}
						}
						html += '</div>';
						$('#column_right').html(html);
					}
				});
			}
		}
	});	
	
	$('#column_right a').live('click', function () {
		if ($(this).attr('class') == 'selected') {
			$(this).removeAttr('class');
		} else {
			$('#column_right a').removeAttr('class');
			
			$(this).attr('class', 'selected');
		}
	});
	
	$('#column_right a').live('dblclick', function () {
		<?php if(isset($_GET['CKEditorFuncNum'])){?>
			window.opener.CKEDITOR.tools.callFunction(<?=$_GET['CKEditorFuncNum'];?>,'<?php echo constant('SERVERNAME');?>/images/upload'+$(this).attr('file'));
			self.close();
		<?php }?>
		<?php if($_GET['field']=='' and !isset($_GET['CKEditorFuncNum'])){?>
		$("#dialogmsg").dialog("destroy");
		$("#dialogmsg").dialog({
			modal: true,
			resizable: false,
			closeOnEscape: true,
			width: 500,
			title: '<b>X (ESC)</b> Link of this file is ' + $(this).attr('file')
		});
		var mpgtype ;
		if($(this).attr('filetype')=='.mpg'){
			
			mpgtype = '<object id="MediaPlayer1" CLASSID="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701"';
			mpgtype += 'standby="Loading Microsoft Windows Media Player components..." type="application/x-oleobject" width="280" height="256">';
			mpgtype += '<param name="fileName" value="<?php echo constant('SERVERNAME');?>/images/upload'+$(this).attr('file')+'">';
			mpgtype += '<param name="animationatStart" value="true">';
			mpgtype += '<param name="transparentatStart" value="true">';
			mpgtype += '<param name="autoStart" value="true">';
			mpgtype += '<param name="showControls" value="true">';
			mpgtype += '<param name="Volume" value="-450">';
			mpgtype += '<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" src="<?php echo constant('SERVERNAME');?>/images/upload'+$(this).attr('file')+'" name="MediaPlayer1" width=280 height=256 autostart=1 showcontrols=1 volume=-450>';
			mpgtype += '</object>';
			}
		$("#dialogmsg").html(" <div id='msgbox_app'>"+mpgtype+" <img src=\""+ $(this).attr('thumb') + "\"/> <br/><?php echo constant('SERVERNAME');?>/images/upload" + $(this).attr('file') + "</div>");
		<?php }elseif($_GET['field']!='' and !isset($_GET['CKEditorFuncNum'])){?>
		
			images = $(this).attr('file');
		imgtype = $(this).attr('filetype');
		parent.$('#<?php echo $_GET['field']; ?>').attr('value',  images);
		parent.$('#<?php echo 'type'.$_GET['field']; ?>').attr('value',  imgtype);
		parent.$('#dialog').dialog('close');
		parent.$('#dialog').remove();			
		<?php }?> 
	});
	$('#create').bind('click', function () {
		var tree = $.tree.focused();
		
		if (tree.selected) {
			$('#dialog').remove();			
			html  = '<div id="dialog">';
			html += 'New folder <input type="text" name="name" value="" /> <input type="button" value="Submit" />';
			html += '</div>';
			$('#column_right').prepend(html);
			
			$('#dialog').dialog({
				title: 'folder',
				resizable: false
			});

			$('#dialog input[type=\'button\']').bind('click', function () {
				$.ajax({
					url: 'filemanagercontroll.php?call=create',
					type: 'POST',
					data: 'directory=' + encodeURIComponent($(tree.selected).attr('directory')) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							$('#dialog').remove();
							
							tree.refresh(tree.selected);
							
							alert(json.success);
						} else {
							alert(json.error);
						}
					}
				});
			});
		} else {
			alert('error no directory');	
		}
	});
	
	$('#delete').bind('click', function () {
		path = $('#column_right a.selected').attr('file');
							 
		if (path) {
			$.ajax({
				url: 'filemanagercontroll.php?call=delete',
				type: 'POST',
				data: 'path=' + path,
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						var tree = $.tree.focused();
					
						tree.select_branch(tree.selected);
						
						alert(json.success);
					}
					
					if (json.error) {
						alert(json.error);
					}
				}
			});				
		} else {
			var tree = $.tree.focused();
			
			if (tree.selected) {
				$.ajax({
					url: 'filemanagercontroll.php?call=delete',
					type: 'POST',
					data: 'path=' + encodeURIComponent($(tree.selected).attr('directory')),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							tree.select_branch(tree.parent(tree.selected));
							tree.refresh(tree.selected);							
							alert(json.success);
						} 
						
						if (json.error) {
							alert(json.error);
						}
					}
				});			
			} else {
				alert('error not select');
			}			
		}
	});
	
	$('#move').bind('click', function () {
		$('#dialog').remove();
		html  = '<div id="dialog">';
		html += 'move to <select name="to"></select> <input type="button" value="Submit" />';
		html += '</div>';

		$('#column_right').prepend(html);
		
		$('#dialog').dialog({
			title: 'move',
			resizable: false
		});

		$('#dialog select[name=\'to\']').load('filemanagercontroll.php?call=folders');
		
		$('#dialog input[type=\'button\']').bind('click', function () {
			path = $('#column_right a.selected').attr('file');
							 
			if (path) {																
				$.ajax({
					url: 'filemanagercontroll.php?call=move',
					type: 'POST',
					data: 'from=' + encodeURIComponent(path) + '&to=' + encodeURIComponent($('#dialog select[name=\'to\']').val()),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							$('#dialog').remove();
							
							var tree = $.tree.focused();
							
							tree.select_branch(tree.selected);
							
							alert(json.success);
						}
						
						if (json.error) {
							alert(json.error);
						}
					}
				});
			} else {
				var tree = $.tree.focused();
				
				$.ajax({
					url: 'filemanagercontroll.php?call=move',
					type: 'POST',
					data: 'from=' + encodeURIComponent($(tree.selected).attr('directory')) + '&to=' + encodeURIComponent($('#dialog select[name=\'to\']').val()),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							$('#dialog').remove();
							
							tree.select_branch('#top');
								
							tree.refresh(tree.selected);
							
							alert(json.success);
						}						
						
						if (json.error) {
							alert(json.error);
						}
					}
				});				
			}
		});
	});

	$('#copy').bind('click', function () {
		$('#dialog').remove();
		
		html  = '<div id="dialog">';
		html += 'copy <input type="text" name="name" value="" /> <input type="button" value="Submit" />';
		html += '</div>';

		$('#column_right').prepend(html);
		
		$('#dialog').dialog({
			title: 'copy',
			resizable: false
		});
		
		$('#dialog select[name=\'to\']').load('filemanagercontroll.php?call=folders');
		
		$('#dialog input[type=\'button\']').bind('click', function () {
			path = $('#column_right a.selected').attr('file');
							 
			if (path) {																
				$.ajax({
					url: 'filemanagercontroll.php?call=copy',
					type: 'POST',
					data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							$('#dialog').remove();
							
							var tree = $.tree.focused();
							
							tree.select_branch(tree.selected);
							
							alert(json.success);
						}						
						
						if (json.error) {
							alert(json.error);
						}
					}
				});
			} else {
				var tree = $.tree.focused();
				
				$.ajax({
					url: 'filemanagercontroll.php?call=copy',
					type: 'POST',
					data: 'path=' + encodeURIComponent($(tree.selected).attr('directory')) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							$('#dialog').remove();
							
							tree.select_branch(tree.parent(tree.selected));
							
							tree.refresh(tree.selected);
							
							alert(json.success);
						} 						
						
						if (json.error) {
							alert(json.error);
						}
					}
				});				
			}
		});	
	});
	
	$('#rename').bind('click', function () {
		$('#dialog').remove();
		
		html  = '<div id="dialog">';
		html += 'rename <input type="text" name="name" value="" /> <input type="button" value="Submit" />';
		html += '</div>';

		$('#column_right').prepend(html);
		
		$('#dialog').dialog({
			title: 'rename',
			resizable: false
		});
		
		$('#dialog input[type=\'button\']').bind('click', function () {
			path = $('#column_right a.selected').attr('file');
							 
			if (path) {		
				$.ajax({
					url: 'filemanagercontroll.php?call=rename',
					type: 'POST',
					data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							$('#dialog').remove();
							
							var tree = $.tree.focused();
					
							tree.select_branch(tree.selected);
							
							alert(json.success);
						} 
						
						if (json.error) {
							alert(json.error);
						}
					}
				});			
			} else {
				var tree = $.tree.focused();
				
				$.ajax({ 
					url: 'filemanagercontroll.php?call=rename',
					type: 'POST',
					data: 'path=' + encodeURIComponent($(tree.selected).attr('directory')) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							$('#dialog').remove();
								
							tree.select_branch(tree.parent(tree.selected));
							
							tree.refresh(tree.selected);
							
							alert(json.success);
						} 
						
						if (json.error) {
							alert(json.error);
						}
					}
				});
			}
		});		
	});
	
	new AjaxUpload('#upload', {
		action: 'filemanagercontroll.php?call=upload',
		name: 'image',
		autoSubmit: false,
		responseType: 'json',
		onChange: function(file, extension) {
			var tree = $.tree.focused();
			
			if (tree.selected) {
				this.setData({'directory': $(tree.selected).attr('directory')});
			} else {
				this.setData({'directory': ''});
			}
			
			this.submit();
		},
		onSubmit: function(file, extension) {
			$('#upload').append('<img src="image/loading.gif" id="loading" style="padding-left: 5px;" />');
		},
		onComplete: function(file, json) {
			if (json.success) {
				var tree = $.tree.focused();
					
				tree.select_branch(tree.selected);
				
				alert(json.success);
			}
			
			if (json.error) {
				alert(json.error);
			}
			
			$('#loading').remove();	
		}
	});
	
	$('#refresh').bind('click', function () {
		var tree = $.tree.focused();
		
		tree.refresh(tree.selected);
	});	
});
-->
</script>
</body>
</html>