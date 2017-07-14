<?php 

class GroupExpenses extends \HXPHP\System\Model
{
	public static function registerGroup(array $post){
		$callbackObj = new \stdClass; //cria uma classe genérica
		$callbackObj->group = null;
		$callbackObj->status = false;
		$callbackObj->errors = array();

		$register = self::create($post);
		
		if($register->is_valid()){ //Se for válido o cadastro ...
			$callbackObj->group = $register;
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

	public static function searchAllGroups(){
		$all_groups = array();
		$array_id_merge = array();
		$array_name_merge = array();
		
		//Encontrando os grupos do BD e armazenando os dados em um array
		$i = 1;
		$stop = 2;
		while ($i < $stop) {
			$group = self::find_by_id($i);

			if (is_null($group)) {
				$stop = 0;
			}
			else {
				$array_id = array($i => $group->id);
				$array_name = array($i => $group->name);

				$array_id_merge = array_merge($array_id_merge, $array_id);
				$array_name_merge = array_merge($array_name_merge, $array_name);

				$stop++;
			}
			$i++;
		}

		$all_groups = array_combine($array_id_merge, $array_name_merge);
		
		return $all_groups;
	}
}