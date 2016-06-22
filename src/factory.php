<?php
namespace Choco;
use \PDO;

class Factory {
	public static function model($file) {
		$var = "\tprotected ";
		$num_values = 0;
		$functions = '';
		//$map = '$map = [';
		$model = Yaml::read($file);
		$start = '';
		$function = '';
		// active record
		$save = '';
		$delete = '';
		$find = '';
		//$select = '';
		$find_one = '';
		$find_array = '';
		$name_entities = '';
		foreach($model as $db=>$entities) {
			$var = $var . '$db = \'' . $db . '\', ';
			foreach($entities as $name_entities=>$values) {
				echo 'entitie ' . $name_entities . ' has been created from ' . $db . "\n";
				$var = $var . '$name = \'' . $name_entities . '\', $__id = \'\', $__select = \'*\', ';
				$start = Self::php_start() . ucwords($name_entities) . ' extends ' . $db  . " {\n";
				$save = $save . "\n\tpublic function save() {\n";
				$save = $save . "\t\t" . '$this->query(\'INSERT INTO ' . $db . '.' . $name_entities . ' values(null, \' . ' . "\n\t\t";
				$delete = $delete . "\n\tpublic function delete" . '($where)' . " {\n";
				$delete = $delete . "\t\t" . '$this->query("DELETE FROM ' . $db . '.' . $name_entities . ' WHERE $where;");';
				$delete = $delete . "\n\t}\n";

				//$select = $select . "\n\tpublic function select" . '($select)' . " {\n";
				//$select = $select . "\t\t" . '$this->__select = $select || \'*\';';
				//$select = $select . "\n\t}\n";

				$find = $find . "\n\tpublic function find" . '($where)' . " {\n";
				$find = $find . "\t\t" . '$query = $this->query("SELECT $this->__select FROM ' . $db . '.' . $name_entities . ' WHERE $where;");' . "\n";
				$find = $find . "\t\t" . '$query = $query->fetchAll(PDO::FETCH_ASSOC);' . "\n";
				$find = $find . "\t\t" . '$num = count($query);' . "\n";
				$find = $find . "\t\t" . 'if ($num == 1) {' . "\n";
				$find = $find . "\t\t\t" . '$query = $query[0];' . "\n";
				//$this->nombres = $query["nombres"];
				$function = $function . "\n\tpublic function id() {\n";
				$function = $function . "\t\t" . 'return $this->__id' . ";\n";
				$function = $function . "\t}\n";

				$find_one = $find_one . "\t\t\t" . '$this->__id = $query[\'id\'] ' . ";\n";
				foreach ($values as $name_value => $property) {
					$num_values = $num_values + 1;
					$var = $var . '$__' . $name_value . " = NULL, ";
					//$map = $map . '\'' . $name_value . '\', ';
					$value = '';
					$function = $function . "\n\tpublic function " . $name_value . '($v = NULL) ' . "{\n";
					$function = $function . "\t\t" . 'if ($v != NULL) ' . "\n";
					$function = $function . "\t\t\t" . '$this->__' . $name_value . ' = $v' . ";\n";
					$function = $function . "\t\t" . 'else ' . "\n";
					$function = $function . "\t\t\t" . 'return $this->__' . $name_value . ";\n";
					$function = $function . "\t}\n";

					$save = $save . "\"'\" . " . '$this->__' . $name_value . " . \"', \" . ";
					
					$find_one = $find_one . "\t\t\t" . '$this->__' . $name_value . ' = $query[\'' . $name_value . "'];\n";

					//$find_array = $find_one . "\t\t\t" . '$this->__' . $name_value . ' = $query[\'' . $name_value . "'];\n";
					/*
					else if ($num > 1) {
			$this->__id = [];
			$this->__nombre = [];
			print_r($this->__id);
			for ($i=0; $i < $num; $i++) { 
				$this->__id[] = $query[$i]['id'];
				$this->__nombres[] = $query[$i]['nombres'];
			}
			
			print_r($this->__id);
		}
					*/
					/*foreach($property as $name_property=>$value_final) {

						// - $value = $value . '\'' . $name_property . '\' => \'' . $value_final . '\', ';
						//$map = $map . '\'' . $name_property . '\' => \'' . $value_final . '\', ';
					}*/
				}
				$save = trim($save, " . \"', \" . ");
				$save = $save . " . \"');\");" . "\n\t}\n";
				$value = trim($value, ', ');
				$find = $find . $find_one . "\t\t" . '}' . "\n";
				$find = $find . "\n\t}\n";
			}
		}
		$var = $var/* . $map*/;
		$var = trim($var, '. ');
		//$var = $var . '], $num_entities = ' . $num_values . ';';
		$var = $var . ' $num_entities = ' . $num_values . ';';
		$activerecord = $save . $delete . /*$select . */$find;
		// file_exists()
		chdir(__DIR__ . '/../../../../');
		if (is_dir('app/'))
			chdir('app/');
		else {
			mkdir('app/');
			chdir('app/');
		}

		if (is_dir('models/'))
			chdir('models/');
		else {
			mkdir('models/');
			chdir('models/');
		}

		if (file_exists($name_entities . '.php'))
			unlink($name_entities . '.php');
		$file_read = fopen($name_entities . '.php', 'c');
		fwrite($file_read, $start . $activerecord . $function . $var . "\n}");
		
		array_push(Self::$models, 'app/models/' . $name_entities . '.php');
	}
	public static function database($db) {
		chdir(__DIR__ . '/../../../');
		$db = Yaml::read($db);
		foreach ($db as $name_database => $property) {
			echo 'database ' . $name_database . ' has been created' . "\n";
			$file = Self::php_start() . ucwords($name_database) . "/* implements \Choco\ActiveRecord */{\n";


			$file = $file . Self::php_database_construct();

			if (isset($property['driver']) && isset($property['host']) && isset($property['user'])) {
				$file = $file . "\tprotected ";
				foreach ($property as $key => $value) {
					$file = $file . '$' . $key . ' = \'' . $value .'\', ' ;
				}
			}
			else
				continue;
			$file = $file . '$conn = NULL, ';
			$file = trim($file, ', ');
			$file = $file . ";\n}";

			chdir(__DIR__ . '/../../../../');


			if (is_dir('app/'))
				chdir('app/');
			else {
				mkdir('app/');
				chdir('app/');
			}

			if (is_dir('models/'))
				chdir('models/');
			else {
				mkdir('models/');
				chdir('models/');
			}


			if (is_dir('db/'))
				chdir('db/');
			else {
				mkdir('db');
				chdir('db');
			}

			if (file_exists($name_database . '.php'))
				unlink($name_database . '.php');
			$file_read = fopen($name_database . '.php', 'c');
			fwrite($file_read, $file);
			array_push(Self::$models, 'app/models/db/' . $name_database . '.php');
		}
	}
	public static function autoloader() {
		$file = "<?php\n";
		foreach (Self::$models as $key => $value) {
			$file = $file . 'require_once \'' . $value . '\'' . ";\n";
		}
		chdir(__DIR__ . '/../../../../');
		if (is_dir('config/')) 
			chdir('config/');
		else {
			mkdir('config/');
			chdir('config/');
		}

		if (file_exists('autoloader.php'))
			unlink('autoloader.php');
		$file_read = fopen('autoloader.php', 'c');
		fwrite($file_read, $file);
	}
	public static function sql($config) {

		// database
		$path = $config . '/database/'; 
		$filenames = scandir($path);
		$num = count($filenames);
		$db = '';
		$driver = '';
		$host = '';
		$user = '';
		$pass = '';
		$name_database = '';
		for ($i = 2; $i < $num; $i++) { 
			$db = Yaml::read($path . $filenames[$i]);
			foreach ($db as $name_database => $property) {
				$$name_database = $name_database;
				if (isset($property['driver']) && isset($property['host']) && isset($property['user'])) {
					foreach ($property as $key => $value) {
						$$key = $value;
					}
				}
			}
			try {
				$db = new PDO($driver . ':host=' . $host . ';', $user, $pass);
				echo 'Connected to database ' . $name_database . "\n";
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			
		}

		// entities
		$path = $config . '/entities/'; 
		$filenames = scandir($path);
		$num = count($filenames);
		$sql = '';
		$delete = '';
		$fill = '';
		$insert = [];
		$num_iterator = 0;
		$fk = '';
		
		//$delete_db = '';
		//$create_db = '';
		$delete_group = '';
		$sql_group = '';
		
		for ($i = 2; $i < $num; $i++) { 
			$entities = Yaml::read($path . $filenames[$i]);
			foreach ($entities as $name_database => $property) {
				//$delete_db = 'DROP DATABASE IF NOT EXISTS' . $name_database . ";\n";
				//$create_db = 'CREATE DATABASE ' . $name_database . ";\n";
				
				foreach ($property as $name_entities => $value) {
					$sql = $sql . 'CREATE TABLE ' . $name_database . '.' . $name_entities . ' (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ';
					$delete = $delete . 'DROP TABLE IF EXISTS '. $name_database . '.' . $name_entities . ";\n";
					foreach ($value as $k => $v) {
						$sql = $sql . $k . ' ';
						if ($v['type'] == 'varchar')
							$sql = $sql . $v['type'] . '(' . $v['size'] . ') ';
						else
							$sql = $sql . $v['type'] . ' ';
						if (isset($v['join']) && $v['join'] != ''){
							$sql = $sql . 'UNSIGNED  ';
							$fk = $fk .'ALTER TABLE ' . $name_database . '.' . $name_entities . ' ADD INDEX(' . $k . ');' . "\n";
							$fk = $fk .'ALTER TABLE ' . $name_database . '.' . $name_entities . ' ADD CONSTRAINT fk_' . $name_entities . '_' . $k . ' ';
							$fk = $fk .'FOREIGN KEY (' . $k . ') REFERENCES ' . $name_database . '.' . $v['join'] .  ' (id); ' . "\n";
						}
						if (isset($v['required']) && $v['required'] == true) 
							$sql = $sql . 'NOT NULL , ';
						else
							$sql = $sql . ' , ';

						if (isset($v['fill'])) {
							$insert[$num_iterator] = $v['fill'];
							$num_iterator = $num_iterator + 1;
						}

						
					}


					$sql = trim($sql, ', ');
					$sql = $sql . ");\n";
					// insert default values
					$num_iterator_for = count($insert);
					if ($insert != [] && $num_iterator_for > 0) {
						$num_iterator_for = count($insert[0]);
					}
					else
						$num_iterator_for = 0;

					if ($insert != [] && $num_iterator_for > 0) {
						$num_iterator_for = count($insert[0]);
						$num_keys = count($insert);
						$fill = $fill . 'INSERT INTO '. $name_database . '.' . $name_entities . ' VALUES';
						for ($j=0; $j < $num_iterator_for; $j++) { 
							$fill = $fill . ' (null, ';
							for ($k=0; $k < $num_keys; $k++) { 
								$fill = $fill . '\'' . $insert[$k][$j] . '\'), ( ';
							}
							$fill = trim($fill, ', (');
							$fill = $fill . ",";
						}
						$fill = trim($fill, ', (');
						$fill = $fill . ";\n";
					}
					$delete_group = $delete_group . $delete; 
					$sql_group = $sql_group . $sql; 
					/*try {
						//var_dump($delete . $sql);
						$db->query($delete . $sql);
						echo $name_database . '.' . $name_entities . ' has been created' . "\n";
					} catch(PDOException $e) {
						echo "error: " . $e->getMessage();
					}*/
					$sql = '';
					$delete = '';
					$num_iterator = 0;
					//var_dump($insert);
					$insert = [];
				}
			}
		}
		try {
			/*chdir(__DIR__ . '/../../../');

			if (file_exists('query.sql'))
				unlink('query.sql');
			$file_read = fopen('query.sql', 'c');
			fwrite($file_read, $delete_group . $sql_group . $fk . $fill);
			*/
			//var_dump($delete_group . $sql_group . $fk . $fill);
			$db->query($delete_group . $sql_group . $fk . $fill);
			echo "finished\n";
		} catch(PDOException $e) {
			echo "error: " . $e->getMessage();
		}
		//$out = $delete_group . $sql_group;
		//$out = $out . $fk . $fill;


		
	}
	public static function gen_sql($config) {
		// database
		$path = $config . '/database/'; 
		$filenames = scandir($path);
		$num = count($filenames);
		$db = '';
		$driver = '';
		$host = '';
		$user = '';
		$pass = '';
		$out = '';
		$name_database = '';
		for ($i = 2; $i < $num; $i++) { 
			$db = Yaml::read($path . $filenames[$i]);
			foreach ($db as $name_database => $property) {
				$$name_database = $name_database;
				if (isset($property['driver']) && isset($property['host']) && isset($property['user'])) {
					foreach ($property as $key => $value) {
						$$key = $value;
					}
				}
			}
		}

		// entities
		$path = $config . '/entities/'; 
		$filenames = scandir($path);
		$num = count($filenames);
		$sql = '';
		$delete = '';
		$fill = '';
		$insert = [];
		$num_iterator = 0;
		$fk = '';
		$delete_group = '';
		$sql_group = '';
		
		//$delete_db = '';
		//$create_db = '';
		
		for ($i = 2; $i < $num; $i++) { 
			$entities = Yaml::read($path . $filenames[$i]);
			foreach ($entities as $name_database => $property) {
				//$delete_db = 'DROP DATABASE IF NOT EXISTS' . $name_database . ";\n";
				//$create_db = 'CREATE DATABASE ' . $name_database . ";\n";
				
				foreach ($property as $name_entities => $value) {
					$sql = $sql . 'CREATE TABLE ' . $name_database . '.' . $name_entities . ' (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ';
					$delete = $delete . 'DROP TABLE IF EXISTS '. $name_database . '.' . $name_entities . ";\n";
					foreach ($value as $k => $v) {
						$sql = $sql . $k . ' ';
						if ($v['type'] == 'varchar')
							$sql = $sql . $v['type'] . '(' . $v['size'] . ') ';
						else
							$sql = $sql . $v['type'] . ' ';
						if (isset($v['join']) && $v['join'] != ''){
							$sql = $sql . 'UNSIGNED  ';
							$fk = $fk .'ALTER TABLE ' . $name_database . '.' . $name_entities . ' ADD INDEX(' . $k . ');' . "\n";
							$fk = $fk .'ALTER TABLE ' . $name_database . '.' . $name_entities . ' ADD CONSTRAINT fk_' . $name_entities . '_' . $k . ' ';
							$fk = $fk .'FOREIGN KEY (' . $k . ') REFERENCES ' . $name_database . '.' . $v['join'] .  ' (id); ' . "\n";
						}
						if (isset($v['required']) && $v['required'] == true) 
							$sql = $sql . 'NOT NULL , ';
						else
							$sql = $sql . ' , ';

						if (isset($v['fill'])) {
							$insert[$num_iterator] = $v['fill'];
							$num_iterator = $num_iterator + 1;
						}

						
					}


					$sql = trim($sql, ', ');
					$sql = $sql . ");\n";
					// insert default values
					$num_iterator_for = count($insert);
					if ($insert != [] && $num_iterator_for > 0) {
						$num_iterator_for = count($insert[0]);
					}
					else
						$num_iterator_for = 0;

					if ($insert != [] && $num_iterator_for > 0) {
						$num_iterator_for = count($insert[0]);
						$num_keys = count($insert);
						$fill = $fill . 'INSERT INTO '. $name_database . '.' . $name_entities . ' VALUES';
						for ($j=0; $j < $num_iterator_for; $j++) { 
							$fill = $fill . ' (null, ';
							for ($k=0; $k < $num_keys; $k++) { 
								$fill = $fill . '\'' . $insert[$k][$j] . '\'), ( ';
							}
							$fill = trim($fill, ', (');
							$fill = $fill . ",";
						}
						$fill = trim($fill, ', (');
						$fill = $fill . ";\n";
					}
					$sql_group = $sql_group . $sql;
					$delete_group = $delete_group . $delete;
					$sql = '';
					$delete = '';
					$num_iterator = 0;
					//var_dump($insert);
					$insert = [];
				}
			}
		}
		$out = $out . $delete_group . $sql_group;
		$out = $out . $fk . $fill;


		chdir(__DIR__ . '/../../../../');
		echo $out;
		if (file_exists('query.sql'))
			unlink('query.sql');
		$file_read = fopen('query.sql', 'c');
		fwrite($file_read, $out);
	}
	private static function php_start() {
		return 
'<?php 
/* generated by Choco ORM
 * docs: comming soon
 * Author: Jeferson De Freitas
 * licence: https://www.gnu.org/licenses/gpl-3.0.html
 */

class ';
	}

	private static function php_database_construct() {
		return 
'	function __construct() {
		try {
			$this->conn = new PDO($this->driver . \':host=\' . $this->host . \';dbname=\' . $this->db . \';charset=utf8mb4\', $this->user, $this->pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		} catch (PDOException $e) {
			print \'Connection error: \' . $e->getMessage() . \'<br/>\';
			die();
		}
	}

	protected function query($query) {
		try {
			return $this->conn->query($query);
		} catch (PDOException $e) {
			print \'Query error: \' . $e->getMessage() . \'<br/>\';
			die();
		}
	}' . "\n";
	}
	private static $models = [];
}