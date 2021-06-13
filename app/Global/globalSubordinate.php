<?php
    if(!function_exists('getHigher')){
        function getHigher(){
            $return     =   [];
            /*
            $listHigher =   App\Models\UsuarioConfig::where('id_usuario',Auth::user()->id)
                            ->whereNotNull('id_superior')
                            ->get();

            foreach ($listHigher as $keyHigher => $valueHigher) {
                $tmpUser    =   App\Models\User::find($valueHigher->id_superior);

                if(!in_array($tmpUser, $return)) {
                    array_push($return, $tmpUser);
                } // if(!in_array($tmpUser, $return)) { ... };
            } // foreach ($listHigher as $keyHigher => $valueHigher) { ... }
            */

            return $return;
        } // function getHigher(){ ... }
    } // if(!function_exists('getHigher')){ ... }

    if(!function_exists('getSubordinates')) {
        function getSubordinates() {
            $return     =   [];

            $tmpData    =   getSubordinate(Auth::user()->id);

            foreach ($tmpData as $keyID => $valueID) {
                if(!is_null($valueID) && $valueID->id != Auth::user()->id) {
                    array_push($return, $valueID);

                    // Busca o subordinado abaixo do subordinado lvl 2
                    $tmpData2   =   getSubordinate($valueID->id);

                    foreach ($tmpData2 as $keyID2 => $valueID2) {
                        if(!is_null($valueID2) && $valueID2->id != Auth::user()->id) {
                            if(!in_array($valueID2, $return)) {
                                array_push($return, $valueID2);
                            } // if(!in_array($valueID2, $return)) { ... }
                        } // if($valueID->id != Auth::user()->id) { ... }
                    } // foreach ($tmpData as $keyID => $valueID) { ... }
                } // if($valueID->id != Auth::user()->id) { ... }
            } // foreach ($tmpData as $keyID => $valueID) { ... }

            return $return;
        } // function getSubordinates() { ... }
    } // if(!function_exists('getSubordinate')) { ... }

    if(!function_exists('getSubordinate')) {
        function getSubordinate($idUser) {
            $return  =   [];

            /*$userData   =   App\Models\UsuarioConfig::where('id_superior',$idUser)
                            ->get();

            foreach ($userData as $key => $value) {
                $tmpUser    =   App\Models\User::find($value->id);

                if(!is_null($tmpUser) && !in_array($tmpUser, $return)) {
                    array_push($return, $tmpUser);
                } // if(!in_array($tmpUser, $return)) { ... };
            } // foreach ($userData as $key => $value) { ... }
            */

            return $return;
        } // function getSubordinate() { ... }
    } // if(!function_exists('getSubordinate')) { ... }