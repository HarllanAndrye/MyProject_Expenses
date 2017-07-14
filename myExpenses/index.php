<?php
	/**
		Framework: https://github.com/brunosantoshx/hxphp/tree/eb7986d1ea8066143e02a29263cf1bed392c737b
		Documentação: https://github.com/brunosantoshx/hxphp-docs

		Se tiver o erro "Fatal error: Class ... not found in ...", verificar a permissão das pastas.
			$ cd opt/
			$ sudo chmod -R 777 /lampp
			$ sudo chmod -R 755 /lampp/phpmyadmin
		Ou verificar o "mod_rewrite" do Apache.

		Video aula:
		https://www.youtube.com/playlist?list=PL2NLqGvZxQu2xMnvPKqLz4f9Pljsj-K4B

		Obs:
			- Quando criar uma nova tabela, coloque-a com o nome en inglês e no plural.
			- Em app/models, coloque o nome no singular, por exemplo: Tabela "people" -> model "person".
			- Tabela com duas palavras, colocar "undescore" e no model as palavras juntas, como foi o caso da LoginAttempts.
	*/

	ob_start();

	ini_set('display_errors', 1);
	set_time_limit(0);

	date_default_timezone_set('America/Sao_Paulo');
	setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT_PATH', dirname(__FILE__) . DS);
    define('APP_PATH', 'app' . DS);
	define('TEMPLATES_PATH', ROOT_PATH . 'templates' . DS);

	define('HXPHP_VERSION', '2.6.3');
    
	/**
	 * Verifica se o autoload do Composer está configurado
	 */
	$composer_autoload = 'vendor' . DS . 'autoload.php';

	if ( ! file_exists($composer_autoload))
		die('Execute o comando: composer install');

	if (version_compare(PHP_VERSION, '5.4.0', '<'))
		die('Atualize seu PHP para a vers&atilde;o: 5.4.0 ou superior.');

	require_once($composer_autoload);

	//Start da sessão
	HXPHP\System\Services\StartSession\StartSession::sec_session_start();

	//Inicio da aplicação
	$app = new HXPHP\System\App(require_once APP_PATH . 'config.php');
	$app->ActiveRecord();
	$app->run();

?>