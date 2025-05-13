<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function FaqIndex () {
        return view('faq');
    }

    public function AboutUsIndex () {
        return view('about-us');
    }

    public function HomeIndex () {
        return view('contact-us');
    }
}
