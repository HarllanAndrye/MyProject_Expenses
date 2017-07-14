<?php
	//Constantes
	$configs = new HXPHP\System\Configs\Config;

	$configs->env->add('development');

	$configs->env->development->baseURI = '/PHP/myExpenses/';

	$configs->env->development->database->setConnectionData(array(
		'host' => 'localhost',
		'user' => 'harllan',
		'password' => '12345',
		'dbname' => 'expensesMonthly_2'
	));

	//Redirecionamento do usuário ao logar
	//Quando fizer o login vai para "home", quando sair vai para a página de login
	$configs->env->development->auth->setURLs('/PHP/myExpenses/home/', '/PHP/myExpenses/login/');

	// ------------------------------------ DEFINIÇÃO DOS MENUS ------------------------------------ //
	//
	/**
		Menu só para o usuário (user) visualizar
		'texto_do_menu/icone_fonte_awesome' => 'Link'
	*/
	$configs->env->development->menu->setMenus(array(
		'Home/home' => '%baseURI%/home',
		'Gastos/tasks' => '%baseURI%/expenses',
		'Gráficos/bar-chart-o' => '%baseURI%/chart',
		'Extras/book' => array(
				'Calendário/calendar' => '%baseURI%/calendar',
				'Bloquear tela/lock' => '%baseURI%/lockscreen'
			)
		), 'user'
	);

	//Menu para administrador
	$configs->env->development->menu->setMenus(array(
		'Home/home' => '%baseURI%/home', //Link relativo: vem de baseURI = /PHP/myExpenses/
		'Gastos/tasks' => '%baseURI%/expenses',
		'Gráficos/bar-chart-o' => '%baseURI%/chart',
		'Usuários/users' => '%baseURI%/users',
		'Extras/book' => array(
				'Calendário/book' => '%baseURI%/calendar',
				'Bloquear tela/book' => '%baseURI%/lockscreen'
			)
		), 'administrator'
	);

	//Menu genérico (qualquer um visualiza)
	$configs->env->development->menu->setMenus(array(
		'Home/dashboard' => '%baseURI%/home',
		'Entrar/dashboard' => '%baseURI%/login'
	));

	/**
		Customizando o menu (documentação):
			https://github.com/brunosantoshx/hxphp-docs/blob/master/08-helpers.md
		Ver exemplo bootstrap:
			http://getbootstrap.com/components/#navbar
	*/
	/**$configs->env->development->menu->setConfigs(array(
		'container' => 'nav',
		'container_class' => 'navbar navbar-default',
		'menu_class' => 'nav navbar-nav'
	));*/
	$configs->env->development->menu->setConfigs(array(
		'container' => 'div',
		'container_class' => 'nav-collapse',
		'menu_class' => 'sidebar-menu'
	));

	//
	// -------------------------------------------------------------------------------------------- //

	/*
		//Globais
		$configs->title = 'Titulo customizado';

		//Configurações de Ambiente - Desenvolvimento
		$configs->env->add('development');

		$configs->env->development->baseURI = '/hxphp/';

		$configs->env->development->database->setConnectionData([
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => '',
			'dbname' => 'hxphp',
			'charset' => 'utf8'
		]);

		$configs->env->development->mail->setFrom([
			'from' => 'Remetente',
			'from_mail' => 'email@remetente.com.br'
		]);

		$configs->env->development->menu->setConfigs([
			'container' => 'nav',
			'container_class' => 'navbar navbar-default',
			'menu_class' => 'nav navbar-nav'
		]);

		$configs->env->development->menu->setMenus([
			'Home/home' => '%siteURL%',
			'Subpasta/folder-open' => [
				'Home/home' => '%baseURI%/admin/have-fun/',
				'Teste/home' => '%baseURI%/admin/index/',
			]
		]);

		$configs->env->development->auth->setURLs('/hxphp/home/', '/hxphp/login/');
		$configs->env->development->auth->setURLs('/hxphp/admin/home/', '/hxphp/admin/login/', 'admin');

		//Configurações de Ambiente - Produção
		$configs->env->add('production');

		$configs->env->production->baseURI = '/';

		$configs->env->production->database->setConnectionData([
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'usuariodobanco',
			'password' => 'senhadobanco',
			'dbname' => 'hxphp',
			'charset' => 'utf8'
		]);

		$configs->env->production->mail->setFrom([
			'from' => 'Remetente',
			'from_mail' => 'email@remetente.com.br'
		]);
	*/


	return $configs;

?>