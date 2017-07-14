<?php 

class IndexController extends \HXPHP\System\Controller
{
    //Carregando o módulo de autenticação
	public function __construct($configs){
		parent::__construct($configs);

		//Alterando o título da página
		$this->view->setTitle('Entrar');
	}
}