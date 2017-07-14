<?php 

class ProfileController extends \HXPHP\System\Controller
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

		//Carregando o Menu
		$this->load(
			'Helpers\Menu',
			$this->request,
			$this->configs,
			$this->auth->getUserRole() //Menu privado, só usuários logados podem ver.
		);

		//Pegando o id do usuário
		$user_id = $this->auth->getUserId();

		//Alterando o título da página e atribuindo um valor a variável user (view_user) da view
		//User::find($user_id): Passando os dados do usuário por padrão para a view
		$this->view->setTitle('Editar perfil')->setVar('user', User::find($user_id));
	}

	public function editAction(){
		$this->view->setFile('edit');

		$user_id = $this->auth->getUserId();

		//http://php.net/manual/en/book.filter.php
		$this->request->setCustomFilters(array(
				'email' => FILTER_VALIDATE_EMAIL
			));

		$post = $this->request->post();
		if (!empty($post)) {
			$updateUser = User::updateProfile($user_id, $post);

			if ($updateUser->status === false) {
				$this->load('Helpers\Alert', array(
						'danger',
						'Ops! Não foi possível atualizar seu perfil. <br />Verifique os erros abaixo:',
						$updateUser->errors
					));
			} 
			else { //Usuário foi bem sucedido (cadastrado)
				//Adicionando imagem: https://github.com/brunosantoshx/class.upload.php
				if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
					//Pegando a imagem que está sendo envida através do formulário
					$uploadUserImage = new upload($_FILES['image']);

					//Se a imagem foi enviada, começa as customizações na imagem
					if ($uploadUserImage->uploaded) {
						//Nome sa imagem: aleatória e em md5
						$image_name = md5(uniqid());
						$uploadUserImage->file_new_name_body = $image_name;

						//extensão da imagem
						$uploadUserImage->file_new_name_ext = 'png';

						$uploadUserImage->resize = true;
						$uploadUserImage->image_x = 200; //200px de largura
						$uploadUserImage->image_ratio_y = true; //Adaptar o y (altura) ao tamnho de x

						//Onde irá salvar a imagem
						// DS é a '/' = Directory Separator
						$dir_path = ROOT_PATH . DS . 'public' . DS . 'uploads' . DS . 'img/users' . DS . $updateUser->user->id . DS;
						$uploadUserImage->process($dir_path);

						if ($uploadUserImage->processed) {
							$uploadUserImage->clean();

							$this->load('Helpers\Alert', array(
								'success',
								'Perfil atualizado com sucesso.'
							));

							//Se existir uma imagem do usuário, apaga ela
							if (!is_null($updateUser->user->image)) {
								unlink($dir_path . $updateUser->user->image);
							}

							$updateUser->user->image = $image_name . '.png';

							$updateUser->user->save(false);
						}
						else {
							$this->load('Helpers\Alert', array(
								'error',
								'Não foi possível atualizar a sua imagem de perfil. -> '.$dir_path,
								$uploadUserImage->error
							));
						}
					}
				}
				else {
					$this->load('Helpers\Alert', array(
						'success',
						'Perfil atualizado com sucesso.'
					));
				}

				$this->view->setVar('user', $updateUser->user); //Atualizar o nome do usuário na tela
				
			}
		}
	}
}