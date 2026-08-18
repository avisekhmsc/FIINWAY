<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function about()      { return view('pages.about'); }
    public function contact()    { return view('pages.contact'); }
    public function careers()    { return view('pages.careers'); }
    public function press()      { return view('pages.press'); }
    public function payments()   { return view('pages.payments'); }
    public function shipping()   { return view('pages.shipping'); }
    public function returnPolicy() { return view('pages.return-policy'); }
    public function terms()      { return view('pages.terms'); }
    public function security()   { return view('pages.security'); }
    public function privacy()    { return view('pages.privacy'); }
    public function sellOnline() { return view('pages.sell-online'); }
}
