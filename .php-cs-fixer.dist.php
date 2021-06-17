<?php

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__.'/src')
	->in(__DIR__.'/tests')
;

$config = new PhpCsFixer\Config();
return $config->setRules([
	'@Symfony' => true
])
	->setFinder($finder)
	;
