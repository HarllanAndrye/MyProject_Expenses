<?php

if (!empty($_REQUEST['year']) && !empty($_REQUEST['month'])) {
    $currentYear = intval($_REQUEST['year']);
    $currentMonth = intval($_REQUEST['month']);
    $dates = array();
    $tmp = array();
    
    $holidays = array(
        '1' => array(
                '01' => 'Confraternização Mundial: Início do calendário anual'
            ),
        '2' => array(),
        '3' => array(),
        '4' => array(
                '21' => 'Tiradentes: Em homenagem ao mártir da Inconfidência Mineira Joaquim José da Silva Xavier, o Tiradentes'
            ),
        '5' => array(
                '01' => 'Dia do trabalhador'
            ),
        '6' => array(),
        '7' => array(),
        '8' => array(),
        '9' => array(
                '07' => 'Independência: Proclamação da Independência do domínio de Portugal'
            ),
        '10' => array(
                '15' => 'Dia do professor'
            ),
        '11' => array(
                '02' => 'Finados: Dia de memória aos mortos',
                '15' => 'Proclamação da República: Transformação do Império em uma República'
            ),
        '12' => array(
                '25' => 'Natal'
            )
    );
    
    foreach($holidays[$currentMonth] as $day => $description){
        if(!empty($day)){
            $date = $currentYear.'-'.str_pad($currentMonth, 2, '0', STR_PAD_LEFT).'-'.$day;
            $tmp = array(
                    'date' => $date,
                    'badge' => false,
                    'title' => 'Feriado de ' . $date,
                    'body' => $description,
                    'footer' => '',
                    'classname' => 'orangeHoliday'
                );
            array_push($dates, $tmp);
        }
    }
    
    echo json_encode($dates);
} else {
    echo json_encode(array());
}
