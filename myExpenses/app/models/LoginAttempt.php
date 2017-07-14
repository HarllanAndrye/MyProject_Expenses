<?php

/**
	Video Aula 8:
	https://www.youtube.com/watch?v=hV0qM9YJbAg&index=8&list=PL2NLqGvZxQu2xMnvPKqLz4f9Pljsj-K4B
*/

class LoginAttempt extends \HXPHP\System\Model
{
	public static function totalAttempts($user_id){
		// count: Retorna a quantidade de tentativas
		// find_all: todos os registros do usuário filtrando pelo user_id
		return count(self::find_all_by_user_id($user_id));
	}

	//Tentativas restantes
	public static function remainingAttempts($user_id){
		return intval(5-self::totalAttempts($user_id));
	}

	public static function registerAttempts($user_id){
		self::create(array(
				'user_id' => $user_id
			));
	}

	//O usuário logou-se, então apaga as tentativas que ele errou
	public static function cleanAttempts($user_id){
		self::delete_all(array(
				'conditions' => array('user_id=?', $user_id)
			));
	}

	//Saber se as tentativas acabaram
	public static function existsAttempts($user_id){
		return self::totalAttempts($user_id) < 5 ? true : false;
	}
}