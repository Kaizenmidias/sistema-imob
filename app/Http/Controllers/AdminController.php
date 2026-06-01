<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Property;
use App\Models\Lead;
use App\Models\Setting;
use App\Models\Page;

class AdminController extends Controller
{
    public function index(): Response
    {
        $properties = Property::count();
        $leads = Lead::count();
        
        return Inertia::render('Admin/Dashboard', [
            'propertiesCount' => $properties,
            'leadsCount' => $leads,
        ]);
    }
    
    public function properties(): Response
    {
        $properties = Property::with(['propertyType', 'photos'])->get();
        return Inertia::render('Admin/Properties', ['properties' => $properties]);
    }
    
    public function createProperty(): Response
    {
        return Inertia::render('Admin/PropertyCreate');
    }
    
    public function leads(): Response
    {
        $leads = Lead::all();
        return Inertia::render('Admin/Leads', ['leads' => $leads]);
    }
    
    public function appearance(): Response
    {
        $settings = Setting::all()->pluck('valor', 'chave');
        return Inertia::render('Admin/Appearance', ['settings' => $settings]);
    }
    
    public function layout(): Response
    {
        $settings = Setting::all()->pluck('valor', 'chave');
        return Inertia::render('Admin/Layout', ['settings' => $settings]);
    }
    
    public function pages(): Response
    {
        $pages = Page::all();
        return Inertia::render('Admin/Pages', ['pages' => $pages]);
    }
    
    public function editPage(Page $page): Response
    {
        return Inertia::render('Admin/PageEdit', ['page' => $page]);
    }
    
    public function settings(): Response
    {
        $settings = Setting::all();
        return Inertia::render('Admin/Settings', ['settings' => $settings]);
    }
}
