<?php
namespace Choco;

Class Session {
	public static function start() {
		session_start();

	}
	public static function close() {
		return session_destroy();

	}
	public static function new($session = []) {
		foreach ($session as $k => $v) {
			$_SESSION[$k] = $v;
		}
	}
	public static function get($session = '') {
		if (isset($_SESSION[$session]))
			return $_SESSION[$session];
		else
			return '';
	}

	public static function exist($session = []) {
		$num = count($session);
		$exist = 0;
		for ($i=0; $i < $num; $i++) { 
			if (isset($_SESSION[$session[$i]])) {
				$exist = $exist + 1;
			}
		}
		return $num == $exist;
	}
}
Session::Start();