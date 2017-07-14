<?php

/**
	Video aula 4:
	https://www.youtube.com/watch?v=7zB5tL064zw&list=PL2NLqGvZxQu2xMnvPKqLz4f9Pljsj-K4B&index=4
*/

class User extends \HXPHP\System\Model
{

	//Relacionamento da tabela 'users' com a tabela 'roles'
	static $belongs_to = array(
		array(
			'role'
		)
	);

	static $validates_presence_of = array(
		array(
			'name',
			'message' => 'O nome é um campo obrigatório.'
		),
		array(
			'email',
			'message' => 'O e-mail é um campo obrigatório.'
		),
		array(
			'username',
			'message' => 'O nome de usuário é um campo obrigatório.'
		),
		array(
			'password',
			'message' => 'A senha é um campo obrigatório.'
		)
 	);

	/**
		Validação de exclusividade
	*/
	static $validates_uniqueness_of = array(
    	array(
    		'username', 
    		'message' => 'Já existe um usuário com este nome de usuário cadastrado!'
    	),
    	array(
    		'email', 
    		'message' => 'Já existe um usuário com este e-mail cadastrado!'
    	)
 	);



	public static function register(array $post){
		$callbackObj = new \stdClass; //cria uma classe genérica
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->errors = array();

		//Retorna uma linha (com id e role) onde o valor da coluna role é igual a "user"
		$role = Role::find_by_role('user');

		if (is_null($role)) {
			array_push($callbackObj->errors, 'A role user não existe. Contate o administrador.');
			return $callbackObj;
		}

		//Unindo os dados do post com o role_id e status
		$user_data = array(
			'role_id' => $role->id,
			'status' => 1
		);

		//Criptografando a senha
		//Retorna a senha criptografada e o "salt"
		$password = \HXPHP\System\Tools::hashHX($post['password']);

		//Sobrepondo o password que era texto. Agora criptografado.
		//Cada indice do array $post corresponde a uma coluna da tabela user
		$post = array_merge($post, $user_data, $password);

		$register = self::create($post);
		
		if($register->is_valid()){ //Se for válido o cadastro ...
			$callbackObj->user = $register;
			$callbackObj->status = true;
			return $callbackObj;
		}

		//Em caso de erro, retorna a mensagem que foi criada no inicio desta classe.
		$errors = $register->errors->get_raw_errors();
		foreach ($errors as $field => $message) {
			array_push($callbackObj->errors, $message[0]);
		}
		return $callbackObj;
	}

	public static function updateProfile($user_id, array $post){
		$callbackObj = new \stdClass; //cria uma classe genérica
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->errors = array();

		//Existe e não está vazio
		if (isset($post['password']) && !empty($post['password'])) {
			//Criptografando a senha
			//Retorna a senha criptografada e o "salt"
			$password = \HXPHP\System\Tools::hashHX($post['password']);

			//Sobrepondo o password que era texto. Agora criptografado.
			//Cada indice do array $post corresponde a uma coluna da tabela user
			$post = array_merge($post, $password);
		}

		$user = self::find($user_id);
		$user->name = $post['name'];
		$user->email = $post['email'];
		$user->username = $post['username'];

		if (isset($post['salt'])) {
			$user->password = $post['password'];
			$user->salt = $post['salt'];
		}

		//Se o e-mail exite na base de dados e o usuário é diferente do que tem esse e-mail no bd
		$exists_mail = self::find_by_email($post['email']);
		if (!is_null($exists_mail) && intval($user_id) !== intval($exists_mail->id)) {
			array_push($callbackObj->errors, 'Já existe um usuário com o e-mail <strong>' . $post['email'] . '</strong> já cadastrado. Por favor, escolha outro e tente novamente.');

			return $callbackObj;
		}

		//Se o username (login) exite na base de dados e o usuário é diferente do que tem esse login no bd
		$exists_username = self::find_by_username($post['username']);
		if (!is_null($exists_username) && intval($user_id) !== intval($exists_username->id)) {
			array_push($callbackObj->errors, 'Já existe um usuário com o login <strong>' . $post['username'] . '</strong> já cadastrado. Por favor, escolha outro e tente novamente.');

			return $callbackObj;
		}

		//false: para pular a validação do activeRecord e atualizar os dados
		$update = $user->save(false);

		if($update){ //Se atualizou ...
			$callbackObj->user = $user;
			$callbackObj->status = true;
			return $callbackObj;
		}

		//Em caso de erro, retorna a mensagem que foi criada no inicio desta classe.
		$errors = $register->errors->get_raw_errors();
		foreach ($errors as $field => $message) {
			array_push($callbackObj->errors, $message[0]);
		}
		return $callbackObj;
	}

	public static function login(array $post){

		$callbackObj = new \stdClass; //cria uma classe genérica
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->code = null; //Código de erro
		$callbackObj->remaining_attempts = null;

		$user = self::find_by_username($post['username']);

		if (!is_null($user)) {
			//Usa o 'salt' para gerar uma senha criptografada igual a do usuário no banco de dados
			$password = \HXPHP\System\Tools::hashHX($post['password'], $user->salt);

			if ($user->status === 1) {
				if (LoginAttempt::existsAttempts($user->id)) {
					//Comparando a senha
					if ($password['password'] === $user->password) {
						$callbackObj->user = $user;
						$callbackObj->status = true;

						loginAttempt::cleanAttempts($user->id);
					}
					else {

						if (LoginAttempt::remainingAttempts($user->id) <= 3) {
							$callbackObj->code = 'tentativas-esgotando';
							$callbackObj->remaining_attempts = LoginAttempt::remainingAttempts($user->id);
						}
						else {
							$callbackObj->code = 'dados-incorretos'; //Passa o code para o 'LoginController.php'	
						}
						//Registrando que o usuário errou a senha
						LoginAttempt::registerAttempts($user->id);
					}
				}
				else { //Se o usuário não tem mais tentativas, bloqueia ele com status=0
					$callbackObj->code = 'usuario-bloqueado';
					$user->status = 0;
					$user->save(false); //false: para pular a validação de email e username do ActiveRecord
				}
			}
			else {
				$callbackObj->code = 'usuario-bloqueado'; // templates/Module/Messages/auth.json
			}
		}
		else {
			$callbackObj->code = 'usuario-inexistente';
		}

		return $callbackObj;
	}

	public static function updatePassword($user, $newPassword){
		//Buscando o usuário na base de dados
		$user = self::find_by_id($user->id);

		//Criptografando a senha
		//Nessa variável temos um array associativo, com senha e salt
		$password = \HXPHP\System\Tools::hashHX($newPassword);

		$user->password = $password['password'];
		$user->salt = $password['salt'];

		return $user->save(false); //false: para pular a validação de email e username do ActiveRecord
	}
}

