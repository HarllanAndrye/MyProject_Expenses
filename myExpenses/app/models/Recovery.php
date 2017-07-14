<?php

class Recovery extends \HXPHP\System\Model
{
	/**
		Associação entre as tabelas 'users' e 'recoveries'
		http://www.phpactiverecord.org/projects/main/wiki/Associations#belongs_to
	*/
	static $belongs_to = array(
		array('user') //nome da tabela no singular
	);

	//Validar o e-mail a ser enviado
	public static function makeValid($user_email){
		$callback_obj = new \stdClass;
		$callback_obj->user = null;
		$callback_obj->code = null;
		$callback_obj->status = false;

		$user_exists = User::find_by_email($user_email);

		if (!is_null($user_exists)) {
			$callback_obj->status = true;
			$callback_obj->user = $user_exists;

			//Excluindo os registros de requisição do usuário 'user_id'
			//Tabela 'recoveries'
			self::delete_all(array(
					'conditions' => array(
							'user_id = ?',
							$user_exists->id
						)
				));
		}
		else {
			$callback_obj->code = 'nenhum-usuario-encontrado';
		}

		return $callback_obj;
	}

	public static function validateToken($token){
		$callback_obj = new \stdClass;
		$callback_obj->user = null;
		$callback_obj->code = null;
		$callback_obj->status = false;

		$validate = self::find_by_token($token);

		if (!is_null($validate)) {
			$callback_obj->user = $validate->user; //O 'user' é por causa do 'belongs_to'
			$callback_obj->status = true;
		}
		else {
			$callback_obj->code = 'token-invalido';
		}

		return $callback_obj;
	}

	//Apagar os registros de determinado usuário na tabela 'recoveries'
	public static function clean($user_id){
		return self::delete_all(array(
				'conditions' => array(
					'user_id = ?',
					$user_id
				)
			));
	}
}

