<?php
require_once(__DIR__ . '/vendor/autoload.php');
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;

class Builder{
	protected $contentRoot = __DIR__ . '/content';
	protected $distRoot = __DIR__ . '/dist';
	protected $markdownConvert;
	public function __construct(){
		$markEnv = new Environment([
			'allow_unsafe_links'=> false,
			'heading_permalink'=> [
				'insert'=> 'after',
				'min_heading_level'=> 2,
			],
			'table'=> [
				'wrap'=>[
					'attributes'=> [
						'class'=> 'tableWrap',
						'enabled'=> 'true',
						'tag'=> 'div',
					],
				],
			],
			'table_of_contents'=> [
				'min_heading_level'=> 2,
			],
		]);
		$markEnv->addExtension(new AttributesExtension());
		$markEnv->addExtension(new CommonMarkCoreExtension());
		$markEnv->addExtension(new DescriptionListExtension());
		$markEnv->addExtension(new FootnoteExtension());
		$markEnv->addExtension(new FrontMatterExtension());
		$markEnv->addExtension(new HeadingPermalinkExtension());
		$markEnv->addExtension(new TableExtension());
		$markEnv->addExtension(new TableOfContentsExtension());
		$this->markdownConvert = new MarkdownConverter($markEnv);
	}
	public function buildContent($baseName = '/'){
		$contentRootLength = strlen($this->contentRoot);
		foreach(glob($this->contentRoot . $baseName . '**') as $file){
			$subPath = substr($file, $contentRootLength);
			$targetPath = preg_replace('/\\.md$/i', '.html', $subPath);
			if(is_file($file)){
				if(!getenv('GITHUB_ACTIONS')){
					echo "file: {$subPath}=> {$subPath}\n";
				}
				file_put_contents($this->distRoot . $targetPath,
					'<!doctype html><title>' . substr($subPath, 1, -3) . '</title>'
					. '<meta content="initial-scale=1,width=device-width" name="viewport" />'
					. "<style><!--\n" . file_get_contents(__DIR__ . '/styles.css') . '--></style>'
					. $this->markdownConvert->convert(file_get_contents($file))
					. '<footer>'
					. '<a href="https://github.com/tobymackenzie/tobymackenzie.github.io">code</a><br />'
					. 'By <a href="https://www.tobymackenzie.com">Toby Mackenzie</a>'
					. '</footer>'
				);
			}else{
				if(!getenv('GITHUB_ACTIONS')){
					echo "dir: {$subPath}=> {$subPath}\n";
				}
				if(!is_dir($this->distRoot . $subPath)){
					mkdir($this->distRoot . $subPath);
				}
				$this->buildContent($subPath . '/');
			}
		}
	}
}
$builder = new Builder();
$builder->buildContent();
