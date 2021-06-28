<?php

    namespace App\Http\Controllers\Page\Cards;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class CardsData extends Controller
    {
        public function listCard(Request $request) {
            try {
                return view('page.card.list');
            } // try { ... }
            catch(Exception $error) {
                dd($error);
            } // catch(Exception $error) { ... }
        } // public function listTask(Request $request) { ... }
    }
