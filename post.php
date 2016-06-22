<?php
namespace Choco;

Class POST {
	public static function start($var = []) {
		foreach ($_POST as $key => $value) {
			Self::$post[$key] = $value;
		}

	}
	public static function exist($var = []) {
		//var_dump(Self::$post);
		$num = count($var);
		$exist = 0;
		for ($i=0; $i < $num; $i++) { 
			//echo "$var[$i] \n";
			//var_dump((isset(Self::$post[$var[$i]]) && Self::$post[$var[$i]] != ''));

			if (isset(Self::$post[$var[$i]]) && Self::$post[$var[$i]] != '') {
				$exist = $exist + 1;
			}
		}
		//echo "$num $exist \n";
		return $num == $exist;
	}
	public static function get($var = NULL) {
		if ($var != NULL) {
			if (Self::exist([$var]))
				return Self::$post[$var];
			else
				return false;
		}
		else
			return Self::$post;
	}
	private static $post = [];
}
POST::Start();