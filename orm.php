<?php
namespace Choco;
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/src/yaml.php';
require_once __DIR__ . '/src/factory.php';

Class ORM {
	public static function start() {
		Self::$config = Yaml::read(__DIR__. "/../../../" . 'choco.yml');
		$filenames = scandir(__DIR__. "/../../../" . Self::$config_path . 'entities/');
		$num = count($filenames);
		for ($i = 2; $i < $num; $i++) { 
			array_push(Self::$entities, $filenames[$i]);
		}
	}
	private static $models_path = 'app/models/', $config_path = 'config/', $entities = [], $config = null;
}
ORM::Start();