<?php
/**
 * @author rain man
 * @date 2011/12/4
 * @site http://rainman.cnblogs.com/
 * @desc 合并CSS和JS文件：
 *          1. 将"*.js.php"和"*.css.php"合并文件，执行后输出到"*.js"和"*.css"，并删除原PHP文件
 *          2. 替换"*.html"和"*.inc"中对"*.js.php"和"*.css.php"的引用
 * @demo 命令行中执行"php combine.php project"或通过浏览器访问"http://domain/combine.php?project"
 */
 
require_once('functions.php');

$folder = $argv[1];
if (!$folder) {
	foreach($_GET as $key=>$value) {
		$folder = $key;
	}
}
$dest_folder = $folder . '_combine';

if (is_dir($folder)) {
	delete_dir($dest_folder);                // 先删除“目标目录”2
	xCopy($folder, $dest_folder);            // 将“源目录”中的文件拷贝至“目标目录”
	$fileList = find_files($dest_folder);    // 获取文件列表
	foreach($fileList as $file) {
		$fileinfo = pathinfo($file);
		// 合并文件
		if (eregi(".js.php$", $file) || eregi(".css.php$", $file)) {
			$dest_file_path = $fileinfo['dirname'] . '/' . $fileinfo['filename'];
			if (file_exists($dest_file_path)) {
				echo 'Error:"' . $file . '" and "' . $dest_file_path . ' is conflict!'. "\n";
			} else {
				$fcontent = execphp_get_contents($file);
				file_put_contents($dest_file_path, $fcontent);
				unlink($file);
			}
		}
		// 替换引用
		if (in_array($fileinfo['extension'], array('html', 'inc'))) {
			$fcontent = file_get_contents($file);
			if (stripos($fcontent, '.js.php') !== false || stripos($fcontent, '.css.php') !== false) {
				$fcontent = preg_replace("/(<script.*).js.php(.*>.*<\/script>)/","$1.js$2", $fcontent);
				$fcontent = preg_replace("/(<link.*).css.php(.*\/>)/","$1.css$2", $fcontent);
				file_put_contents($file, $fcontent);		
			}
		}
	}
	echo "Combine CSS and JavaScript for \"{$folder}\" OK\n";
} else {
	echo "Error:\"{$folder}\" is not a folder \n";
}
?>