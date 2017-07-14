<?php

class RecoverController extends \HXPHP\System\Controller
{

	//Carregando o módulo de autenticação
	public function __construct($configs){
		parent::__construct($configs);

		//Carregando o serviço de autenticação
		$this->load(
				'Services\Auth',
				$configs->auth->after_login,
				$configs->auth->after_logout,
				true //usuário redirecionado quando o login for bem sucedido
			);

		$this->auth->redirectCheck(true);

		//Alterando o título da página
		$this->view->setTitle('Redefinição de senha');

		//Adicionando o módulo de mensagens
		$this->load('Modules\Messages', 'password-recovery'); // password-recovery.json
		$this->messages->setBlock('alerts');
	}

	//Solicitar a redefinição de senha
	public function requestAction(){
		$this->view->setFile('index');

		//Validar o email
		$this->request->setCustomFilters(array(
				'email' => FILTER_VALIDATE_EMAIL
			));

		$email = $this->request->post('email');

		$error = null;

		if (!is_null($email) && $email !== false) {
			$validate = Recovery::makeValid($email);

			if ($validate->status === false) {
				$error = $this->messages->getByCode($validate->code);
			}
			else { //Se o usuário existe
				//Serviço PasswordRecovery que gera um token
				//Caminho completo para o usuário redefinir a senha
				$this->load(
					'Services\PasswordRecovery',
					$this->configs->site->url . $this->configs->baseURI . 'recover/reset/'
					);

				//Adicionando a solicitação na tabela 'recoveries'
				//status=0 pois a solicitação está pendente
				Recovery::create(array(
						'user_id' => $validate->user->id,
						'token' => $this->passwordrecovery->token,
						'status' => 0
					));

				//Bloco messages do password-recovery.json
				$message = $this->messages->messages->getByCode('link-enviado', array(
						'message' => array(
							$validate->user->name,
							$this->passwordrecovery->link,
							$this->passwordrecovery->link
						)
					));

				//var_dump($message); //Ver o link para redefinição de senha

				/**
					Link de redefinição de senha será enviado por e-mail.
					Para isso, um servidor de e-mail precisa estar configurado.
					(https://www.youtube.com/watch?v=KWezZNoHe1w&index=10&list=PL2NLqGvZxQu2xMnvPKqLz4f9Pljsj-K4B)
				*/
				$this->load('Services\Email');

				$sendEmail = $this->email->send(
					$validate->user->email,
					$message['subject'],
					$message['message'] . 'Administrador',
					array(
						'email' => $this->configs->mail->from_mail,
						'remetente' => $this->configs->mail->from
					)
				);

				if ($sendEmail === false) {
					$this->messages->getByCode('email-nao-enviado');
				}
			}
		}
		else {
			$error = $this->messages->getByCode('nenhum-usuario-encontrado');
		}

		if (!is_null($error)) {
			$this->load('Helpers\Alert', $error);
		}
		else {
			$success = $this->messages->getByCode('link-enviado');

			$this->view->setFile('blank');

			$this->load('Helpers\Alert', $success);
		}

	}

	//Redefinir a senha
	public function resetAction($token){
		//Validar o token
		$validateToken = Recovery::validateToken($token);

		$error = null;

		if ($validateToken->status === false) {
			$error = $this->messages->getByCode($validateToken->code);
		}
		else {
			/**
				Atribuindo valor a variável 'view_token'.
				Mesmo sendo 'view_token', só coloca a palavra depois do '_' (underscore). Padrão do framework.
			*/
			$this->view->setVar('token', $token);
		}

		if (!is_null($error)) {
			$this->view->setFile('blank');
			$this->load('Helpers\Alert', $error);
		}
	}

	//Alterar a senha
	public function changePasswordAction($token){
		//Exibir por padrão a view (página) de redefinição de senha
		$this->view->setFile('reset');

		//Validar o token
		$validateToken = Recovery::validateToken($token);

		$error = null;

		if ($validateToken->status === false) {
			$this->view->setFile('blank'); //Muda a view se receber a mensagem de erro
			$error = $this->messages->getByCode($validateToken->code);
		}
		else { //Se o token foi validado
			$this->view->setVar('token', $token); //Atribuindo a variável na view
			$password = $this->request->post('password');

			if (!is_null($password)) {
				$updatePassword = User::updatePassword($validateToken->user, $password);

				if ($updatePassword === true) {
					Recovery::clean($validateToken->user->id);
					//Alterando o caminho e página (formulário de login)
					$this->view->setPath('login')->setFile('index');

					//Mensagem de sucesso ao redefinir a senha
					$success = $this->messages->getByCode('senha-redefinida');
					$this->load('Helpers\Alert', $success);
				}
			}
		}

		if (!is_null($error)) {
			
			$this->load('Helpers\Alert', $error);
		}
	}

}
