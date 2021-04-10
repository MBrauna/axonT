<?php
    if(!function_exists('getPeriodic')){
        function getPeriodic($id){
            try {
                switch ($id) {
                    case 1:
                        return 'dia(s)';
                        break;
                    case 4:
                        return 'mes(es)';
                        break;
                    case 7:
                        return 'ano(s)';
                        break;
                    
                    default:
                        return 'unidade(s)';
                        break;
                }

                return 'unidade';
            }
            catch(Exception $error) {
                return 'unidades';
            }
        }
    }

    if(!function_exists('getAllPeriodics')){
        function getAllPeriodics(){
            try {
                return [
                    (object)[
                        'id'    =>  1,
                        'name'  =>  'dia(s)'
                    ],
                    (object)[
                        'id'    =>  4,
                        'name'  =>  'mes(es)'
                    ],
                    (object)[
                        'id'    =>  7,
                        'name'  =>  'ano(s)'
                    ]
                ];
            }
            catch(Exception $error) {
                return [];
            }
        }
    }