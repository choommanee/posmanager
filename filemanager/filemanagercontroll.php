<?php
error_reporting ( 0 );
include ("lib/json.php");
include ("lib/image.php");
class filemanager {
	public $dirname = 'images/upload';
	
	public $allowed = array ('.jpg', '.jpeg', '.png', '.gif','.flv','.mpg','.swf','.doc','.docx','.xls','.xlsx','.ppt','.pptx','.pps','.ppsx','.odt','.zip','.rar','.accdb' );
	//public $allowed2 = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif', 'application/x-shockwave-flash', 'video/mpeg','application/zip','application/x-rar-compressed','application/pdf','application/msword','application/vnd.ms-excel','application/vnd.ms-powerpoint');
	function getDestination($path = 'images') {
		$this->dirname = $path;
	}
	
	function dir() {
		$json = array ();
		if (! isset ( $_POST ['directory'] )) {
			$directory = $this->dirname;
		} else {
			$directory = $this->dirname . $_POST ['directory'];
		}
		$directories = glob ( $directory . '/*', GLOB_ONLYDIR );
		if ($directories) {
			$i = 0;
			
			foreach ( $directories as $directory ) {
				$json [$i] ['data'] = basename ( $this->UTFEncode ( $directory ) );
				$json [$i] ['attributes'] ['directory'] = $this->UTFEncode ( substr ( $directory, strlen ( $this->dirname ) ) );
				$children = glob ( rtrim ( $directory, '/' ) . '/*', GLOB_ONLYDIR );
		
				if ($children) {
					$json [$i] ['children'] = ' ';
				}
				$i ++;
			}
		}
		
		$JsonEn = new Json ();
		echo $JsonEn->encode ( $json );
		//$this->response->setOutput(Json::encode($json));		
	}
	
	function files() {
		$json = array ();
		//echo $this->dirname.'|'.$_POST ['directory'];
		if (! isset ( $_POST ['directory'] )) {
		    $directory = $this->dirname;
		} else {
			$directory = $this->dirname .  $this->UTFDecode($_POST ['directory']);
		}
		
		//$allowed = array ('.jpg', '.jpeg', '.png', '.gif','.flv','.mpg','.swf','.doc','.docx','.xls','.xlsx','.ppt','.pptx','.pdf','.zip','.rar' );
		$allowed = array ('.jpg', '.jpeg', '.png', '.gif','.flv','.mpg','.swf','.doc','.docx','.xls','.xlsx','.ppt','.pptx','.pps','.ppsx','.pdf','.odt','.zip','.rar' ,'.accdb');
		$files = glob ( rtrim ( $directory, '/' ) . '/*' );
		//print_r($files);
		foreach ( $files as $file ) {
			if (is_file ( $file )) {
				$ext = strrchr ( $file, '.' );
			} else {
				$ext = '';
			}
			//echo $ext;
			if (in_array ( strtolower ( $ext ), $this->allowed )) {
				$size = filesize ( $file );
				
				$i = 0;
				
				$suffix = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
				
				while ( ($size / 1024) > 1 ) {
					$size = $size / 1024;
					$i ++;
				}
				switch ($ext) {
					case '.mpg':
						$json [] = array ('file' =>$this->UTFEncode(substr ( $file, strlen ( $this->dirname ) )),
								  'filename' => basename (  $this->UTFEncode($file)),
								  'filetype' => $ext,
								  'size' => round ( substr ( $size, 0, strpos ( $size, '.' ) + 4 ), 2 ) . $suffix [$i],
								  'thumb' => '../../images/mpgtype.png');
					break;
					case '.swf':
						$json [] = array ('file' =>$this->UTFEncode(substr ( $file, strlen ( $this->dirname ) )),
								  'filename' => basename (  $this->UTFEncode($file)),
								  'filetype' => $ext,
								  'size' => round ( substr ( $size, 0, strpos ( $size, '.' ) + 4 ), 2 ) . $suffix [$i],
								  'thumb' => '../../images/mpgtype.png');
						break;
					case '.doc':
					case '.docx':
						$json [] = array ('file' =>$this->UTFEncode(substr ( $file, strlen ( $this->dirname ) )),
								  'filename' => basename (  $this->UTFEncode($file)),
								  'filetype' => $ext,
								  'size' => round ( substr ( $size, 0, strpos ( $size, '.' ) + 4 ), 2 ) . $suffix [$i],
								  'thumb' => 'images/docIcon/doc.png');
						break;
					case '.xls':
					case '.xlsx':
						$json [] = array ('file' =>$this->UTFEncode(substr ( $file, strlen ( $this->dirname ) )),
								  'filename' => basename (  $this->UTFEncode($file)),
								  'filetype' => $ext,
								  'size' => round ( substr ( $size, 0, strpos ( $size, '.' ) + 4 ), 2 ) . $suffix [$i],
								  'thumb' => 'images/docIcon/xls.png');
							break;
					case '.mdb':
					case '.accdb':
						$json [] = array ('file' =>$this->UTFEncode(substr ( $file, strlen ( $this->dirname ) )),
								  'filename' => basename (  $this->UTFEncode($file)),
								  'filetype' => $ext,
								  'size' => round ( substr ( $size, 0, strpos ( $size, '.' ) + 4 ), 2 ) . $suffix [$i],
								  'thumb' => 'images/docIcon/mdb.png');
						break;
					case '.ppt':
					case '.pptx':
						$json [] = array ('file' =>$this->UTFEncode(substr ( $file, strlen ( $this->dirname ) )),
								  'filename' => basename (  $this->UTFEncode($file)),
								  'filetype' => $ext,
								  'size' => round ( substr ( $size, 0, strpos ( $size, '.' ) + 4 ), 2 ) . $suffix [$i],
								  'thumb' => 'images/docIcon/ppt.png');
						break;
					case '.pdf':
						$json [] = array ('file' =>$this->UTFEncode(substr ( $file, strlen ( $this->dirname ) )),
								  'filename' => basename (  $this->UTFEncode($file)),
								  'filetype' => $ext,
								  'size' => round ( substr ( $size, 0, strpos ( $size, '.' ) + 4 ), 2 ) . $suffix [$i],
								  'thumb' => 'images/docIcon/mdb.png');
						break;
					default:
						$json [] = array ('file' =>$this->UTFEncode(substr ( $file, strlen ( $this->dirname ) )),
								  'filename' => basename (  $this->UTFEncode($file)), 
								  'filetype' => $ext,
								  'size' => round ( substr ( $size, 0, strpos ( $size, '.' ) + 4 ), 2 ) . $suffix [$i],
								  'thumb' => $this->resize ( substr ( $file, strlen ( $directory ) ), 100, 100 ) );
						
				}
				
			}
			//print_r($json);
		}
		
		$JsonEn = new Json ();
		echo $JsonEn->encode ( $json );
	}
	public function create() {
		
		$json = array ();
		
		if (isset ( $_POST ['directory'] )) {
			if (isset ( $_POST ['name'] ) || $_POST ['name']) {
				$directory = rtrim ( $this->dirname . str_replace ( '../', '', $_POST ['directory'] ), '/' );
				
				if (! is_dir ( $directory )) {
					$json ['error'] = 'error directory';
				}
				
				if (file_exists ( $directory . '/' . str_replace ( '../', '', $_POST ['name'] ) )) {
					$json ['error'] = 'error directory exists';
				}
			} else {
				$json ['error'] = 'error name';
			}
		} else {
			$json ['error'] = 'error directory';
		}
		
		if (! isset ( $json ['error'] )) {
			mkdir ( $directory . '/' . str_replace ( '../', '', $this->UTFDecode($_POST ['name']) ), 0777 );
			
			$json ['success'] = 'folder create success';
		}
		
		$JsonEn = new Json ();
		echo $JsonEn->encode ( $json );
	}
	public function delete() {
		
		$json = array ();
		
		if (isset ( $_POST ['path'] )) {
			$path = rtrim ( $this->dirname . str_replace ( '../', '', $this->UTFDecode($_POST ['path']) ), '/' );
			
			if (! file_exists ( $path )) {
				$json ['error'] = 'error select';
			}
			
			if ($path == rtrim ( $this->dirname, '/' )) {
				$json ['error'] = 'delete error';
			}
		} else {
			$json ['error'] = 'select error';
		}
		
		if (! isset ( $json ['error'] )) {
			if (is_file ( $path )) {
				unlink ( $path );
			} elseif (is_dir ( $path )) {
				$this->recursiveDelete ( $path );
			}
			
			$json ['success'] = 'Success delete';
		}
		
		$JsonEn = new Json ();
		echo $JsonEn->encode ( $json );
	}
	
	protected function recursiveDelete($directory) {
		if (is_dir ( $directory )) {
			$handle = opendir ( $directory );
		}
		
		if (! $handle) {
			return FALSE;
		}
		
		while ( false !== ($file = readdir ( $handle )) ) {
			if ($file != '.' && $file != '..') {
				if (! is_dir ( $directory . '/' . $file )) {
					unlink ( $directory . '/' . $file );
				} else {
					$this->recursiveDelete ( $directory . '/' . $file );
				}
			}
		}
		closedir ( $handle );
		rmdir ( $directory );
		return TRUE;
	}
	
	public function folders() {
		echo $this->UTFEncode($this->recursiveFolders ($this->dirname) );
	}
	
	public function move() {		
		$json = array ();
		
		if (isset ( $_POST ['from'] ) && isset ( $_POST ['to'] )) {
			$from = rtrim ( $this->dirname . str_replace ( '../', '', $this->UTFDecode($_POST ['from']) ), '/' );
			
			if (! file_exists ( $from )) {
				$json ['error'] = 'error missing';
			}
			
			if ($from == $this->dirname) {
				$json ['error'] = 'can\'t move default directory';
			}
			
			$to = rtrim ( $this->dirname . str_replace ( '../', '', $_POST ['to'] ), '/' );
			
			if (! file_exists ( $to )) {
				$json ['error'] = 'can\'t move ';
			}
			
			if (file_exists ( $to . '/' . basename ( $from ) )) {
				$json ['error'] = 'directory exists';
			}
		} else {
			$json ['error'] = 'directory error';
		}
		
		if (! isset ( $json ['error'] )) {
			rename ( $from, $to . '/' . basename ( $from ) );
			
			$json ['success'] = 'move success';
		}
		
		$JsonEn = new Json ();
		echo $JsonEn->encode ( $json );
	}
	
	public function copy() {
		
		$json = array ();
		
		if (isset ( $_POST ['path'] ) && isset ( $_POST ['name'] )) {
			if ((strlen ( $this->UTFDecode($_POST ['name']) ) < 3) || (strlen ( $this->UTFDecode($_POST ['name']) ) > 255)) {
				$json ['error'] = 'filename error';
			}
			
			$old_name = rtrim ( $this->dirname . str_replace ( '../', '', $this->UTFDecode($_POST ['path']) ), '/' );
			
			if (! file_exists ( $old_name ) || $old_name == $this->dirname) {
				$json ['error'] = 'notcopy';
			}
			
			if (is_file ( $old_name )) {
				$ext = strrchr ( $old_name, '.' );
			} else {
				$ext = '';
			}
			
			$new_name = dirname ( $old_name ) . '/' . str_replace ( '../', '', $this->UTFDecode($_POST ['name']) . $ext );
			
			if (file_exists ( $new_name )) {
				$json ['error'] = 'files exists';
			}
		} else {
			$json ['error'] = 'select error';
		}
		
		if (! isset ( $json ['error'] )) {
			if (is_file ( $old_name )) {
				$json($old_name);
				copy ( $old_name, $new_name );
			} else {
				$this->recursiveCopy ( $old_name, $new_name );
			}
			
			$json ['success'] = 'copy success';
		}
		
		$JsonEn = new Json ();
		echo $JsonEn->encode ( $json );
	}
	
	function recursiveCopy($source, $destination) {
		$directory = opendir ( $source );
		
		@mkdir ( $destination );
		
		while ( false !== ($file = readdir ( $handle )) ) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir ( $source . '/' . $file )) {
					$this->recursiveCopy ( $source . '/' . $file, $destination . '/' . $file );
				} else {
					copy ( $source . '/' . $file, $destination . '/' . $file );
				}
			}
		}
		
		closedir ( $directory );
	}
	
	function resize($filename, $width, $height) {
		//echo  $this->dirname . $filename;
		if (! file_exists ( $this->dirname . $filename ) || ! is_file ( $this->dirname . $filename )) {
			return $filename;
		}
		
		$old_image = $filename;
		$new_image = 'cache' . substr ( $filename, 0, strrpos ( $filename, '.' ) ) . '-' . $width . 'x' . $height . '.jpg';
		
		if (! file_exists ( $this->dirname.'/' . $new_image ) || (filemtime ( $this->dirname .'/'. $old_image ) > filemtime ( $this->dirname.'/' . $new_image ))) {
			$path = '';
			
			$directories = explode ( '/', dirname ( str_replace ( '../', '', $new_image ) ) );
			
			foreach ( $directories as $directory ) {
				$path = $path . '/' . $directory;
				
				if (! file_exists ( $this->dirname .'/'. $path )) {
					@mkdir ( $this->dirname .'/'. $path, 0777 );
				}
			}
			
			$image = new Image ( $this->dirname.'/' . $old_image );
			$image->resize ( $width, $height );
			$image->save ( $this->dirname .'/'. $new_image );
		}

		if (isset ( $this->request->server ['HTTPS'] ) && (($this->request->server ['HTTPS'] == 'on') || ($this->request->server ['HTTPS'] == '1'))) {
			return $this->dirname.'/' . $this->UTFEncode($new_image);
		} else {
			return $this->dirname .'/'. $this->UTFEncode($new_image);
		}
	}
	
	protected function recursiveFolders($directory) {
		$output = '';
		
		$output .= '<option value="' . substr ( $directory, strlen ( $this->dirname ) ) . '">' . substr ( $directory, strlen ( $this->dirname ) ) . '</option>';
		
		$directories = glob ( rtrim ( str_replace ( '../', '', $directory ), '/' ) . '/*', GLOB_ONLYDIR );
		
		foreach ( $directories as $directory ) {
			$output .= $this->recursiveFolders ( $directory );
		}
		
		return $output;
	}
	
	public function rename() {
		
		$json = array ();
		if (isset ( $_POST ['path'] ) && isset ( $_POST ['name'] )) {
			if ((strlen ( $this->UTFDecode ( $_POST ['name'] ) ) < 3) || (strlen (  $this->UTFDecode ( $_POST ['name'] ) ) > 255)) {
				$json ['error'] = 'filename error';
			}
			
			$old_name = rtrim ( $this->dirname . str_replace ( '../', '',  $this->UTFDecode($_POST ['path']) ), '/' );
			
			if (! file_exists ( $old_name ) || $old_name == $this->dirname) {
				$json ['error'] = 'can\'t not rename defualt forder';
			}
			
			if (is_file ( $old_name )) {
				$ext = strrchr ( $old_name, '.' );
			} else {
				$ext = '';
			}
			
			$new_name = dirname ( $old_name ) . '/' . str_replace ( '../', '',  $this->UTFDecode($_POST ['name']) . $ext );
			
			if (file_exists ( $new_name )) {
				$json ['error'] = 'files exists ';
			}
		}
		
		if (! isset ( $json ['error'] )) {
			rename ( $old_name, $new_name );
			
			$json ['success'] = 'rename success';
		}
		
		$JsonEn = new Json ();
		echo $JsonEn->encode ( $json );
	}
	
	public function upload() {
		$json = array ();
		
		if (isset ( $_POST ['directory'] )) {
			if (isset ( $_FILES ['image'] ) && $_FILES ['image'] ['tmp_name']) {
				if ((strlen ( $_FILES ['image'] ['name']  ) < 3) || (strlen (  $_FILES ['image'] ['name']) > 255)) {
					$json ['error'] = 'filename error';
				}
				
				$directory = rtrim ( $this->dirname . str_replace ( '../', '',  $_POST ['directory'] ), '/' );
				
				if (! is_dir ( $directory )) {
					$json ['error'] = 'directory error';
				}
				
				if ($_FILES ['image'] ['size'] > 3000000000) {
					$json ['error'] = 'error file size';
				}
				//echo $_FILES ['image'] ['type'];
				$allowed = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif', 'application/x-shockwave-flash', 'video/mpeg','application/zip','application/x-rar-compressed','application/pdf','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel','application/octet-stream','application/msaccess');
				
				if (! in_array ( $this->UTFDecode($_FILES ['image'] ['type']), $allowed )) {
					$json ['error'] = 'error file type';//$_FILES ['image'] ['type'];
				}
				
				//$allowed = array ('.jpg', '.jpeg', '.png', '.gif','.flv','.mpg','.swf','.doc','.docx','.xls','.xlsx','.ppt','.pptx','.pdf','.zip','.rar' );
				$allowed = array ('.jpg', '.jpeg', '.png', '.gif','.flv','.mpg','.swf','.doc','.docx','.xls','.xlsx','.ppt','.pptx','.pps','.ppsx','.pdf','.odt','.zip','.rar','.accdb' );
				if (! in_array ( strtolower ( strrchr (  $_FILES ['image'] ['name'], '.' ) ), $allowed )) {
					$json ['error'] = 'error file type';//$_FILES ['image'] ['type'];
				}
				
				if ($_FILES ['image'] ['error'] != UPLOAD_ERR_OK) {
					$json ['error'] = 'error upload file ' . $_FILES ['image'] ['error'];
				}
			} else {
				$json ['error'] = 'file error';
			}
		} else {
			$json ['error'] = 'directory error';
		}
		//echo  basename (  $_FILES ['image'] ['name'] ) ;
		if (! isset ( $json ['error'] )) {
			
			if (@move_uploaded_file (  $_FILES ['image'] ['tmp_name'], $directory . '/' . basename (  $_FILES ['image'] ['name'] ) )) {
				$json ['success'] = 'uploaded success';
			} else {
				$json ['error'] = 'uploaded error';
			}
		}

		$JsonEn = new Json ();
		echo $JsonEn->encode ( $json );
	}
	
	public function image() {
		// $_POST ['image'];
		//$list = explode($delimiter, $string);
		if (isset ( $_POST ['image'] )) {
			echo $this->resize (  $this->UTFDecode($_POST ['image']), 100, 100 );
		}
	}
	function UTFEncode($string, $encoding = 'default') {
		global $config;
		$appEncoding = 'TIS-620';
		if (strtolower ( $encoding ) == 'default') {
			$fromEncoding = $appEncoding;
		} else {
			$fromEncoding = $encoding;
		}
		
		return iconv ( $fromEncoding, 'UTF-8', $string );
	}
	function UTFDecode($string, $encoding = 'default') {
		global $config;
		$appEncoding = 'TIS-620';
		if (strtolower ( $encoding ) == 'default') {
			$targetEncoding = $appEncoding;
		} else {
			$targetEncoding = $encoding;
		}
		
		return iconv ( 'UTF-8', $targetEncoding, $string );
	
	}
}

if ($_GET ['call'] != '') {
	$callData = new filemanager ();
	$method = $_GET ['call'];
	$callData->{$method} ();
}
?>