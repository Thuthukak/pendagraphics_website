<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServicesController extends Controller
{
    public function index()
    {
        
        return response()->json(Service::all());
        
    }
    public function indexWebDesign()
    {
        return view('services.web-design');
    }

    public function indexGraphicDesign()
    {
        return view('services.graphic-design');
    }

    public function indexProductDesign()
    {
        return view('services.product-design');
    }

    public function indexIdentityDesign()
    {
        return view('services.identity-design');
    }

    public function indexECommerce()
    {
        return view('services.e-commerce');
    }

    public function indexDigitalMarketing()
    {
        return view('services.digital-marketing');
    }

}
