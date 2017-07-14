<?php

class RegistrationController extends \HXPHP\System\Controller
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

		/**
			Se a página for pública e o usuário estiver logado, a função redirectCheck(true) não
			deixa ir para a página pública (de cadastro) e redireciona para a página privada (pois
			o usuário já está logado).
			Ou seja, usuário logado só acessa página restrita.
		*/
		$this->auth->redirectCheck(true);

		//Carregando o Menu
		$this->load(
			'Helpers\Menu',
			$this->request,
			$this->configs
		);
        
        $this->view->setTitle('Criar conta');
	}

	public function registerAction(){
		$this->view->setFile('index');

		//http://php.net/manual/en/book.filter.php
		$this->request->setCustomFilters(array(
				'email' => FILTER_VALIDATE_EMAIL
			));

		$post = $this->request->post();
		if (!empty($post)) {
			$registrationUser = User::register($post);

			if ($registrationUser->status === false) {
				$this->load('Helpers\Alert', array(
						'danger',
						'Ops! Não foi possível efetuar seu cadastro. <br />Verifique os erros abaixo:',
						$registrationUser->errors
					));
			} 
			else { //Usuário foi bem sucedido (cadastrado)
				/**
					@harllan
						Corrigido um erro: quando cadastrava o usuário, o nível de acesso não estava
						sendo passado para a view. Quando ia acessar um menu que precisa desse nível,
						dava erro e não exibia o menu.
						Acrescentado: $registrationUser->user->role->role
				*/
				$this->auth->login($registrationUser->user->id, $registrationUser->user->username, $registrationUser->user->role->role);
			}
		}
	}

}
