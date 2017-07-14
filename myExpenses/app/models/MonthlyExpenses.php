<?php 

class MonthlyExpenses extends \HXPHP\System\Model
{
	
	public static function registerExpenses(array $post, $user_id){
		$callbackObj = new \stdClass; //cria uma classe genérica
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->errors = array();

		//Retorna uma linha (com id e nome do grupo) onde o valor da coluna id é igual ao id passado no campo value do cadastro
		$group = GroupExpenses::find_by_id($post['group']);

		if (is_null($group)) {
			array_push($callbackObj->errors, 'O grupo '. $post['group'] .' não existe. Contate o administrador.');
			return $callbackObj;
		}

		//Unindo os dados do post com o nome do grupo e user_id
		$user_data = array(
			'user_id' => $user_id,
			'group' => $group->name,
			'group_id' => $group->id
		);

		$post = array_merge($post, $user_data);
		//return $post;
		
		$register = self::create($post);
		
		if($register->is_valid()){ //Se for válido o cadastro ...
			$callbackObj->user = $register;
			$callbackObj->status = true;
			return $callbackObj;
		}

		//Em caso de erro, retorna a mensagem que foi criada no inicio desta classe.
		$errors = $register->errors->get_raw_errors();
		foreach ($errors as $field => $message) {
			array_push($callbackObj->errors, $message[0]);
		}
		return $callbackObj;
	}

	// Retorna o nomes dos meses anteriores a partir do mês atual, passando um período como parâmetro
	/*
	private static function get_nameMonths($numberMonths){
        $months = array(1 => 'janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro');
        $today = getdate();
        $current_month = $today['mon'];
        $result = array();
        for($i=0; $i < $numberMonths; $i++){
            $result[] = $months[$current_month];
            $current_month--;
            if($current_month == 0){
                $current_month = 12;
            }
        }
        return $result;
    }
    */

    // Retorna as datas anteriores a partir do mês atual, passando um período como parâmetro
	private static function get_dates($numberMonths){
        $i = 0;
        $today = date("Y-m");
        $dates = array();
        $tmpDate = "";
        
        $days = array(
            '1' => 31, 
            '2' => 28, 
            '3' => 31, 
            '4' => 30, 
            '5' => 31, 
            '6' => 30, 
            '7' => 31, 
            '8' => 31, 
            '9' => 30, 
            '10' => 31, 
            '11' => 30, 
            '12' => 31
        );

        while($i < $numberMonths){
            $tmpDate = date('Y-m',strtotime("-{$i} month",strtotime($today)));
            $month = intval(substr($tmpDate, -2));

            for ($day=1; $day <= $days[$month]; $day++) {
    			array_push($dates, $tmpDate ."-". $day);
    		}

            $i ++;
        }

        return $dates;
    }

	public static function searchUserExpensesYear($user_id, $currentMonth){
		$dates = self::get_dates(12);

		// Retorna todos os registros do BD, filtrado por "user_id" e "date" (mês).
		$userExpenses = self::find_all_by_user_id_and_date($user_id, $dates);
		
		$totalExpensesCurrentMonth = 0; // Gasto total do mês atual
		$totalExpensesYear = 0; // Gasto total dos últimos 12 meses
		$expensesYear = array(); // Armazena os gastos dos últimos 12 meses (gasto em cada mês)
		$tmp = 0;
		$monthNew = 0;
		$monthOld = 0;

		if(!is_null($userExpenses)){
			foreach ($userExpenses as $amount => $value) {
				$totalExpensesYear += $value->amount;
				//$month = $value->date->format("n"); //http://www.phpactiverecord.org/docs/ActiveRecord/DateTime

				//Para o mês atual
				if ($value->date->format("n") == $currentMonth) {
					$totalExpensesCurrentMonth += $value->amount;
				}

				// Atribui valor apenas no primeiro registro
				if($amount == 1){
					$monthOld = $value->date->format("n"); // Armazena o mês antigo "do foreach"
				}
				
				//Para todos os meses
				$monthNew = $value->date->format("n"); // Armazena o novo mês "do foreach"
				// Isso é para poder comparar: se são iguais "soma os valores e não insere no array", se diferente "insere no array os dados e inicia nova soma para o próximo mês".
				if ($monthNew == $monthOld) {
					$tmp += $value->amount;
				} else {
					array_push($expensesYear, number_format($tmp,2,'.','')); // "$tmp" está no padrão americano, com ponto para separar o decimal, pois no gráfico da página inicial dar erro se for vírgula
					$tmp = $value->amount;
					$monthOld = $monthNew;
				}

				// "if" acrescentado porque quando chega no final do loop as variáveis "$monthOld" e "$monthNew" ainda são iguais e por isso não passam no teste anterior,
				// não inserindo os dados no array.
				if (($amount+1) == count($userExpenses)) {
					array_push($expensesYear, number_format($tmp,2,'.',''));
				}
			}

			// Removendo o índice 0 (zero) que sempre fica vazio
			unset($expensesYear[0]);
		}

		// Obs.: O array "$expensesYear" retorna os valores do último mês até o atual, ou seja, o último índice do array é o mês atual.
		
		$result = array(
			'totalExpensesMonth' => $totalExpensesCurrentMonth,
			'totalExpensesYear' => $totalExpensesYear,
			'expensesYearArray' => $expensesYear
		);

		return $result;
	}
    
    // Retirada de: http://php.net/manual/en/function.array-combine.php#111668
    private static function array_combine_($keys, $values){
        $result = array();
        foreach ($keys as $i => $k) {
            $result[$k][] = $values[$i];
        }
        array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
        return    $result;
    }
    
    // Retorna os gastos do mês passado como parâmetro
    public static function searchUserExpensesMonth($user_id, $month){
        $dates = array();
		for ($day=1; $day <= date("d"); $day++) { 
			array_push($dates, date("Y") ."-". $month ."-". $day);
		}
        
        $userExpenses = self::find_all_by_user_id_and_date($user_id, $dates);
        
        $expensesMonth = array();
        $datesMonth = array();
        $groupName = array();
        $groupExpense = array();
        $dayNew = 0;
        $dayOld = 0;
        $tmpAmount = 0;
        $tmpDate = "";
        
        if(!is_null($userExpenses)){
            foreach($userExpenses as $amount => $value){
                // Atribui valor apenas no primeiro registro
				if($amount == 1){
					$dayOld = $value->date->format("d");
				}
				
				$dayNew = $value->date->format("d"); // Armazena o novo dia "do foreach"
                
				// Isso é para poder comparar:
                //      se são iguais "soma os valores e não insere no array",
                //      se diferente "insere no array os dados e inicia nova soma para o próximo mês".
				if ($dayNew === $dayOld) {
					$tmpAmount += $value->amount;
                    $tmpDate = $value->date->format("d-m-Y");
				} else {
                    array_push($expensesMonth, number_format($tmpAmount, 2, '.', ''));
                    array_push($datesMonth, $tmpDate);

					$tmpAmount = $value->amount;
                    $tmpDate = $value->date->format("d-m-Y");
					$dayOld = $dayNew;
				}
                
                // "if" acrescentado porque quando chega no final do loop as variáveis "$dayOld" e "$dayNew" ainda são iguais
                // e por isso não passam no teste anterior, não inserindo os dados no array.
				if (($amount+1) == count($userExpenses)) {
                    array_push($expensesMonth, number_format($tmpAmount, 2, '.', ''));
                    array_push($datesMonth, $tmpDate);
				}
                
                array_push($groupExpense, number_format($value->amount, 2, '.', ''));
                array_push($groupName, $value->group);
            }
            // Removendo o índice 0 (zero) que sempre fica vazio
			unset($expensesMonth[0]);
            unset($datesMonth[0]);
        }
        $monthExpenses = array_combine($datesMonth, $expensesMonth);
        // Ordenando o array: por exemplo, uma despesa pode ser registrada em outro dia diferente da data dessa despesa.
        //      Isso faz o array ficar desordenado e bagunçar o gráfico.
        ksort($monthExpenses); // Ordena pela chave
        
        // ---- Soma as despesas por grupo (start) ---- //
        $tmpGroups = self::array_combine_($groupName, $groupExpense);
        // Reiniciando os array, pois os dados deles já esntão em "$tmpGroups"
        $groupName = array();
        $groupExpense = array();
        $tmp_other_value = 0;
        foreach($tmpGroups as $name => $value){
            if(is_array($value)){
                 foreach($value as $other_value){
                     $tmp_other_value += $other_value;
                 }
                 array_push($groupExpense, number_format($tmp_other_value, 2, '.', ''));
                 array_push($groupName, $name);
                 $tmp_other_value = 0;
            }
            else{
                   array_push($groupExpense, number_format($value, 2, '.', ''));
                   array_push($groupName, $name);
            }
        }
        // array_combine: Creates an array by using one array for keys and another for its values
        $groupsNameExpenses = array_combine($groupName, $groupExpense);
        // ----- Soma as despesas por grupo (end) ----- //
        
        $result = array(
                    'expensesMonth' => $monthExpenses,
                    'expensesGroups' => $groupsNameExpenses
                );
        return $result;
    }
}





