<?php

class LoginController extends \HXPHP\System\Controller
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

		//Carregando o Menu
		$this->load(
			'Helpers\Menu',
			$this->request,
			$this->configs
			//$this->auth->getUserRole() //Menu privado, só usuários logados podem ver.
		);

		//Alterando o título da página da view login
		$this->view->setTitle('Entrar');
	}

	public function indexAction(){
		/**
			Se a página for pública e o usuário estiver logado, a função redirectCheck(true) não
			deixa ir para a página pública (de cadastro) e redireciona para a página privada (pois
			o usuário já está logado).
			Ou seja, usuário logado só acessa página restrita.
		*/
		$this->auth->redirectCheck(true);
	}

	public function signinAction(){
		$this->auth->redirectCheck(true);

		$this->view->setFile('index'); //Chamando a view index.phtml

		$post = $this->request->post();

		if (!empty($post)) {
			$login = User::login($post); //Chama a função 'login' na class 'User'.

			if ($login->status === true) {
				//Pasando: id do usuário, nome e nível de acesso dele
				$this->auth->login($login->user->id, $login->user->username, $login->user->role->role);
			}
			else {
				//Carregando os erros/alertas do json
				$this->load('Modules\Messages', 'auth');
				$this->messages->setBlock('alerts');
				$error = $this->messages->getByCode($login->code, array(
						'message' => $login->remaining_attempts
					));

				$this->load('Helpers\Alert', $error);
			}
		}
	}

	public function logoutAction(){
		return $this->auth->logout();
	}
}
