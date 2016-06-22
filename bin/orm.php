<?php
namespace Choco;
use Choco\Yaml;
require_once __DIR__ . '/../lib/choco/orm.php';

class Terminal {
	public static function start($argv) {
		$num = count($argv);
		if ($num == 1) {
			Self::nothing();
		} 
		else if ($num == 2) {
			switch ($argv[1]) {
				case 'insert':
					Self::insert();
					break;

				case 'sql':
					Self::sql();
					break;

				case 'model':
					Self::model();
					break;

				case 'reverse':
					Self::reverse();
					break;

				case '--help':
				case '-h':
					Self::help();
					break;
				
				default:
					Self::default($argv);
					break;
			}
		}
		else if ($num == 3) {
			Self::default($argv);
		}	
	}
	private static function nothing() {
		echo "error: no se especificó una operación (utilice -h para ayuda)\n";
	}
	private static function insert() {
		if (is_file(__DIR__ . '/../choco.yml'))
			$models = Yaml::read(__DIR__ . '/../choco.yml');
		else {
			$models['config'] = __DIR__ . '/config';
			$models['models'] = __DIR__ . '/app/models';;
		}

		Factory::sql(__DIR__ . '/../' . $models['config']);
	}
	private static function sql() {
		if (is_file(__DIR__ . '/../choco.yml'))
			$models = Yaml::read(__DIR__ . '/../choco.yml');
		else {
			$models['config'] = __DIR__ . '/config';
			$models['models'] = __DIR__ . '/app/models';;
		}

		Factory::gen_sql(__DIR__ . '/../' . $models['config']);
	}
	private static function model() {
		if (is_file(__DIR__ . '/../choco.yml'))
			$models = Yaml::read(__DIR__ . '/../choco.yml');
		else {
			$models['config'] = __DIR__ . '/config';
			$models['models'] = __DIR__ . '/app/models';;
		}

		// database
		$path = __DIR__ . '/../' . $models['config'] . '/database/'; 
		$filenames = scandir($path);
		$num = count($filenames);
		for ($i = 2; $i < $num; $i++) { 
			Factory::database($path . $filenames[$i]);
			//array_push(Self::$entities, $filenames[$i]);
		}

		// entities
		$path = __DIR__ . '/../' . $models['config'] . '/entities/'; 
		$filenames = scandir($path);
		$num = count($filenames);
		for ($i = 2; $i < $num; $i++) { 
			Factory::model($path . $filenames[$i]);
			//array_push(Self::$entities, $filenames[$i]);
		}

		// autoloader
		Factory::autoloader();
	}
	private static function reverse() {
		echo "reverse\n";
	}
	private static function help() {
		echo "operaciones:\n";
		echo "uso:  choco-orm <operación>\n";
		echo "operaciones:\n";
		echo "    choco-orm insert\n";
		echo "    choco-orm model\n";
		echo "    choco-orm reverse\n";
	}
	private static function default($argv) {
		echo "choco-orm: " . $argv[1] . " es una opción inválida\n";
	}
}

Terminal::start($argv);
