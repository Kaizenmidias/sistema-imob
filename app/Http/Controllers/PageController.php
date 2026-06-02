<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        $page = Page::where('slug', 'quem-somos')->first() ?? Page::where('slug', 'sobre')->first();
        return Inertia::render('About', ['page' => $page]);
    }

    public function offMarket(): Response
    {
        return Inertia::render('OffMarket');
    }

    public function exclusiveManagement(): Response
    {
        return Inertia::render('ExclusiveManagement');
    }

    public function evaluate(): Response
    {
        return Inertia::render('Evaluate');
    }

    public function partnerAgent(): Response
    {
        return Inertia::render('PartnerAgent');
    }

    public function show(Page $page): Response
    {
        return Inertia::render('PageShow', ['page' => $page]);
    }
}
