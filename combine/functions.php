<?php
/**
 * 获取目录下所有文件
 * @param $path {String} 目录
 * @return Array
 */
function find_files($path){
	$fileList = array();
	if (!is_dir($path)) {
		return $fileList;
	}
	$handel = opendir($path);	
	while (($file = readdir($handel)) !== false) {
		$path_full = $path . '/' . $file;
		if ($file != '.' && $file != '..') {
			if (is_dir($path_full)) {
				$result = find_files($path_full);
				foreach($result as $value) {
					$fileList[] = $value;
				}
			} else {
				$fileList[] = $path_full;
			}
		}
	}
	closedir($handel);
	return $fileList;
}

/**
 * 执行php文件，返回输出内容
 * @param $path {String} php文件路径
 * @return String
 */
function execphp_get_contents($path){
	ob_start();
	@include($path);
	return ob_get_clean();
}

/**
 * 复制文件夹及子文件
 * @param $source {String} 源目录
 * @param $dest {String} 目标目录
 * @param $child {Boolean} 是否复制子文件，true:复制，默认值；false:不复制
 * @return Boolean
 */
function xCopy($source, $dest, $child = true) {
	if (!is_dir($source)) { return false; } 
	if (!is_dir($dest)) { mkdir($dest, 0777); }
	$handel = opendir($source);	
	while (($file = readdir($handel)) !== false) {	
		$source_path_full = $source . '/' . $file;	// 源完整路径
		$dest_path_full =  $dest . '/' . $file;		// 目标完整路径
		if ($file != '.' && $file != '..') {
			if (is_dir($source_path_full) && $child) {
				xcopy($source_path_full, $dest_path_full);
			} else {
				copy($source_path_full, $dest_path_full);
			}
		}
	}
	return true; 
}

/**
 * 删除文件夹
 * @param $dir {String} 目录
 * @return Boolean
 */
function delete_dir($path) {
	if (!is_dir($path)) { return false; } 
    //先删除目录下的文件：
	$handel=opendir($path);
	while (($file = readdir($handel)) !== false) {	
        if ($file != "." && $file != "..") {
            $path_full = $path . "/" . $file;
            if (is_dir($path_full)) {
                delete_dir($path_full);
            } else {
                unlink($path_full);
            }
        }
    }
    closedir($handel);
    //删除当前文件夹：
    if(rmdir($path)) {
        return true;
    } else {
        return false;
    }
}
?>