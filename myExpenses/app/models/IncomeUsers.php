<?php 

class IncomeUsers extends \HXPHP\System\Model
{

	public static function registerIncome(array $post, $user_id){
		$callbackObj = new \stdClass; //cria uma classe genérica
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->errors = array();

		//Array criado com os nomes iguais aos do BD
		$user_data = array(
			'user_id' => $user_id,
			'amount' => $post['incomeAmount']
		);

		//Removendo o campo "incomeAmount" do array.
		unset($post['incomeAmount']);

		$post = array_merge($user_data, $post);
		
		//Verificando se o registro já existe no BD
		// Verificar também o mês: se não verificar, ele não insere um novo registro, apenas atualiza, pois o usuário existe.
		// No BD pode existir mais de um registro por usuário, só não pode ter mais de um registro para o mesmo mês.
		$exists_user = self::find($user_id);
		if ( (!is_null($exists_user)) && ($exists_user->month == $post['month']) ) {
			//Existe: Soma o valor do bd com o passado na página pelo usuário. A soma é só no campo "addicional".
			$exists_user->additional += $post['additional'];

			$update = $exists_user->save(false);

			if($update){ //Se atualizou ...
				$callbackObj->user = $exists_user;
				$callbackObj->status = true;
				return $callbackObj;
			}
		}
		else { //Novo registro
			$register = self::create($post);

			if($register->is_valid()){ //Se for válido o cadastro ...
				$callbackObj->user = $register;
				$callbackObj->status = true;
				return $callbackObj;
			}
		}

		//Em caso de erro, retorna a mensagem que foi criada no inicio desta classe.
		$errors = $register->errors->get_raw_errors();
		foreach ($errors as $field => $message) {
			array_push($callbackObj->errors, $message[0]);
		}
		return $callbackObj;
	}

	public static function searchUserIncome($user_id){
		//$incomeUser = self::find_by_user_id($user_id);
		$incomeUser = self::find_all_by_user_id($user_id); // Retorna todos registros que existir "by user_id"

		// Pega o último registro, caso exista vários para o mesmo usuário
		if (count($incomeUser) > 1) {
			$incomeUser = $incomeUser[count($incomeUser) - 1];
		}

        if(!is_null($incomeUser) && !empty($incomeUser)){
            // Mês no BD tem que ser igual ao mês atual
            if($incomeUser->month == date('m')){
                $result = array(
                        'amount' => $incomeUser->amount,
                        'additional' => $incomeUser->additional,
                        'month' => $incomeUser->month
                    );
                return $result;
            }
        }
        
		
	}

}