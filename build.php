<?php
function buildDir($baseName = '/'){
	$contentRoot = __DIR__ . '/content';
	$contentRootLength = strlen($contentRoot);
	$distRoot = __DIR__ . '/dist';
	foreach(glob($contentRoot . $baseName . '**') as $file){
		$subPath = substr($file, $contentRootLength);
		$targetPath = preg_replace('/\\.md$/i', '.html', $subPath);
		if(is_file($file)){
			if(!getenv('GITHUB_ACTIONS')){
				echo "file: {$subPath}=> {$subPath}\n";
			}
			file_put_contents($distRoot . $targetPath, '<!doctype html><title>' . $subPath . '</title><pre><code>' . file_get_contents($file) . '</code></pre>');
		}else{
			if(!getenv('GITHUB_ACTIONS')){
				echo "dir: {$subPath}=> {$subPath}\n";
			}
			if(!is_dir($distRoot . $subPath)){
				mkdir($distRoot . $subPath);
			}
			buildDir($subPath . '/');
		}
	}
}
buildDir();
