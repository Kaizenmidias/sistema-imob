<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        return Inertia::render('About');
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
}
