<?php

    namespace App\Http\Controllers\API\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class TypeTaskPermission extends Controller
    {
        public function selectType(Request $request) {
            try {
                $returnData =   [
                    (object)[
                        'name'      =>  'MANUAL',
                        'permission'=>  true,
                    ],
                    (object)[
                        'name'      =>  'AUTOMATICO',
                        'permission'=>  false, // Por padrão qualquer opção que não seja manual fica desabilitado
                    ],
                ]
            } // try { ... }
            catch(Exception $error) {

            } // catch(Exception $error) { ... }
        } // public function selectType(Request $request) { ... }
    } // class TypeTaskPermission extends Controller { ... }
