<?php 

/*
AQUI COLOCAR OS ELEMENTOS PARA CADASTRAR AS DESPESAS 
Tem que criar a tabela "monthly_expenses" e seus campos:
      id
      user_id (despesa do usuário id)
      date (data da despesa colocada pelo usuário)
      amount (Valor gasto em R$)
      description (descrição do gasto - e.g., xerox, manuteção na moto, ...)
      group (grupos da despesas - e.g., Saúde, Alimentação, Estudos, Combustível, manuteção veículo, ...)
      group_id
      created_at (data da criação do cadastro da despesa - automático)
Criar tabela para os grupos das despesas "group_expenses" e seus campos:
      id
      name
Tem que ter outra tabela para colocar o valor do salário do usuário, para calcular quanto resta do seu dinheiro em relação ao seus gastos.
Tabela "income_users" e seus campos:
      id
      user_id
      amount
      additional (outros valores que podem entrar para acrescentar a renda. esse campo será atualizado sempre que surgir nova renda extra (soma com 
                  o que já tem no banco de dados))
      month (mês do salário e extras - valor padrão do campo, mês atual, caso o usuário não insira esse dado)

OPCIONAL:
- Colocar uma janela ao lado que vai atualizando após os cadastro das despesas. Essa janela mostra as 4 últimas despesas cadastradas.
*/

class ExpensesController extends \HXPHP\System\Controller
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
		$this->view->setTitle('Despesas')->setVar('user', $user);
	}

	public function registerExpensesAction(){
		$this->view->setFile('index');

		$user_id = $this->auth->getUserId();
		$post = $this->request->post();

		//var_dump($post); //Ver o conteúdo do post
		
		if (!empty($post)) {
			$registrationExpense = MonthlyExpenses::registerExpenses($post, $user_id);
			//var_dump($registrationExpense);
			
			if ($registrationExpense->status === false) {
				$this->load('Helpers\Alert', array(
						'danger',
						'Ops! Não foi possível efetuar o cadastro da despesa. <br />Verifique os erros abaixo:',
						$registrationExpense->errors
					));
			} 
			else {
				$this->load('Helpers\Alert', array(
						'success',
						'Cadastro realizado com sucesso.'
					));
			}
		}
	}

	public function registerIncomeAction(){
		$this->view->setFile('index');

		$user_id = $this->auth->getUserId();
		$post = $this->request->post();

		if (!empty($post)) {
			$registrationIncome = IncomeUsers::registerIncome($post, $user_id);
			
			if ($registrationIncome->status === false) {
				$this->load('Helpers\Alert', array(
						'danger',
						'Ops! Não foi possível efetuar o cadastro da sua renda mensal. <br />Verifique os erros abaixo:',
						$registrationExpense->errors
					));
			} 
			else {
				$this->load('Helpers\Alert', array(
						'success',
						'Cadastro realizado com sucesso.'
					));
			}
		}
	}

	public function registerGroupAction(){
		$this->view->setFile('index');

		$post = $this->request->post();

		if (!empty($post)) {
			$registrationGroup = GroupExpenses::registerGroup($post);
			
			if ($registrationGroup->status === false) {
				$this->load('Helpers\Alert', array(
						'danger',
						'Ops! Não foi possível efetuar o cadastro do grupo. <br />Verifique os erros abaixo:',
						$registrationExpense->errors
					));
			} 
			else {
				$this->load('Helpers\Alert', array(
						'success',
						'Cadastro realizado com sucesso.'
					));
			}
		}
	}

}