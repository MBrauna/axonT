<?php

    namespace App\Http\Controllers\API\Util;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class AccessCompany extends Controller
    {
        public function getCompanies(Request $request) {
            try {
                $idCompany  =   $request->input('idCompany');


                if(is_null($idCompany)) {
                    return response()->json(getAccess(), 200);
                }
                else {
                    $returnData =   [];
                    $idCompany  =   intval($idCompany);

                    foreach (getAccess() as $keyAccess => $valueAccess) {
                        if($valueAccess->id_empresa === $idCompany) {
                            array_push($returnData,$valueAccess);
                        } // if($valueAccess->id_empresa === $idCompany) { ... }
                    } // foreach (getAccess() as $keyAccess => $valueAccess) { ... }


                    return response()->json($returnData, 200);
                }
            } // try { ... }
            catch(Exception $error) {
                return response()->json([], 500);
            } // catch(Exception $error) { ... }
        } // public function getCompanies(Request $request) { ... }
    }
