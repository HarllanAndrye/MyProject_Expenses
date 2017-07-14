<?php 

class HomeController extends \HXPHP\System\Controller
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

		$this->auth->redirectCheck(); //Sem o 'true', pois é uma página privada.

		//Pegando o id do usuário
		$user_id = $this->auth->getUserId();
		$user = User::find($user_id);
		/**
			Pegando o nível de acesso (role), se houver alteração no banco de dados é só recarregar a página que as alterações são aplicadas.
		*/
		$role = Role::find($user->role_id);

		//Carregando o Menu
		$this->load(
			'Helpers\Menu',
			$this->request,
			$this->configs,
			//$this->auth->getUserRole() //Menu privado, só usuários logados podem ver.
			$role->role
		);

		//Alterando o título da página e atribuindo um valor a variável user (view_user) da view
		$this->view->setTitle('Administrativo')->setVar('user', $user);
	}

	public function blockedAction(){
		//Bloqueio por nível de acesso
		$this->auth->roleCheck(array(
			'administrator',
			'user'
		));
	}
}