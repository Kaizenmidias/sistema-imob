<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Blog');
    }

    public function show(string $slug): Response
    {
        return Inertia::render('Blog');
    }
}
