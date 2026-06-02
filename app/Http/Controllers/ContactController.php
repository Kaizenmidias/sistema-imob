<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        $page = Page::where('slug', 'contato')->first();
        $settings = Setting::query()->pluck('valor', 'chave');

        return Inertia::render('Contact', [
            'page' => $page,
            'settings' => $settings,
        ]);
    }
}
