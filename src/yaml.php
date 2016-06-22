<?php
namespace Choco;
use Symfony\Component\Yaml\Yaml as Yaml_symfony;
use Symfony\Component\Yaml\Exception\ParseException;

class Yaml {
	public static function read($file) {
		if (is_file($file)) {
			try {
				$value = Yaml_symfony::parse(file_get_contents($file));
			} catch (ParseException $e) {
				printf("Unable to parse the YAML string: %s", $e->getMessage());
			}
			return $value;
		}
		else
			trigger_error("invalid path " . $file, E_USER_ERROR);
	}
}