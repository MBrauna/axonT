<?php

    namespace App\Http\Controllers\Page;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class MainPage extends Controller
    {
        public function startPage(Request $request) {
            try {
                return view('page.mainPage');
            }
            catch(Exception $error) {
                return view('page.errorPage');
            }
        } // public function startPage(Request $request) { ... }
    }
