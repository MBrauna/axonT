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