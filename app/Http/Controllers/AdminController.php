<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\BusinessType;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\PropertyType;
use App\Models\SpecialCategory;
use App\Models\Lead;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Redirect::route('admin.dashboard');
        }

        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($validated['remember'] ?? false);

        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $remember)) {
            return Redirect::back()
                ->withErrors(['email' => 'Email ou senha inválidos.'])
                ->withInput(['email' => $validated['email']]);
        }

        $request->session()->regenerate();

        return Redirect::intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('login');
    }

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
        $properties = Property::with(['propertyType', 'businessType', 'photos'])->get();
        return Inertia::render('Admin/Properties', ['properties' => $properties]);
    }
    
    public function createProperty(): Response
    {
        $propertyTypes = PropertyType::orderBy('nome_tipo')->orderBy('nome_subtipo')->get();
        $businessTypes = BusinessType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name']);
        $specialCategories = SpecialCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name']);
        $generatedReferenceCode = $this->generateUniqueCodigoReferencia();

        return Inertia::render('Admin/PropertyCreate', [
            'propertyTypes' => $propertyTypes,
            'businessTypes' => $businessTypes,
            'specialCategories' => $specialCategories,
            'generatedReferenceCode' => $generatedReferenceCode,
        ]);
    }

    public function storeProperty(Request $request)
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'codigo_referencia' => ['nullable', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'tipo_propriedade_id' => ['required', 'integer', 'exists:property_types,id'],
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'valor' => ['required', 'string'],
            'endereco' => ['required', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', 'size:2'],
            'quartos' => ['nullable', 'integer', 'min:0'],
            'banheiros' => ['nullable', 'integer', 'min:0'],
            'garagens' => ['nullable', 'integer', 'min:0'],
            'show_in_home_selecao_especial' => ['nullable', 'boolean'],
            'show_in_home_mais_procurados' => ['nullable', 'boolean'],
            'show_in_home_visto_recentemente' => ['nullable', 'boolean'],
            'featured_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'special_category_ids' => ['nullable', 'array'],
            'special_category_ids.*' => ['integer', 'exists:special_categories,id'],
        ]);

        $slug = $this->generateUniquePropertySlug($validated['titulo']);
        $codigoReferencia = $this->normalizeCodigoReferencia($validated['codigo_referencia'] ?? null);
        if ($codigoReferencia === '') {
            $codigoReferencia = $this->generateUniqueCodigoReferencia();
        }
        $codigoAnuncio = $this->generateUniqueCodigoAnuncio();
        $businessType = BusinessType::find($validated['business_type_id']);
        $valor = $this->parseBrlCurrency($validated['valor']);

        $property = Property::create([
            ...collect($validated)->except(['featured_image', 'gallery_images', 'special_category_ids'])->all(),
            'codigo_referencia' => $codigoReferencia,
            'slug' => $slug,
            'codigo_anuncio' => $codigoAnuncio,
            'moeda' => 'BRL',
            'ativo' => true,
            'valor' => $valor,
            'operacao' => $this->mapBusinessTypeNameToLegacyOperacao($businessType?->name),
        ]);

        if (!empty($validated['special_category_ids'])) {
            $property->specialCategories()->sync($validated['special_category_ids']);
        }

        $photosToCreate = [];
        $order = 1;

        if ($request->hasFile('featured_image')) {
            $featured = $request->file('featured_image');
            $path = Storage::disk('public')->putFile("properties/{$property->id}", $featured);
            $url = url('/storage/' . $path);
            $photosToCreate[] = [
                'property_id' => $property->id,
                'arquivo' => $path,
                'url' => $url,
                'principal' => true,
                'ordem' => 0,
            ];
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images', []) as $file) {
                $path = Storage::disk('public')->putFile("properties/{$property->id}", $file);
                $url = url('/storage/' . $path);
                $photosToCreate[] = [
                    'property_id' => $property->id,
                    'arquivo' => $path,
                    'url' => $url,
                    'principal' => false,
                    'ordem' => $order,
                ];
                $order++;
            }
        }

        if (!empty($photosToCreate)) {
            PropertyPhoto::insert($photosToCreate);
        }

        return Redirect::route('admin.properties.edit', ['property' => $property->id]);
    }

    public function editProperty(Property $property): Response
    {
        $property->load(['photos', 'specialCategories']);

        $propertyTypes = PropertyType::orderBy('nome_tipo')->orderBy('nome_subtipo')->get();
        $businessTypes = BusinessType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name']);
        $specialCategories = SpecialCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/PropertyCreate', [
            'propertyTypes' => $propertyTypes,
            'businessTypes' => $businessTypes,
            'specialCategories' => $specialCategories,
            'property' => $property,
            'selectedSpecialCategoryIds' => $property->specialCategories->pluck('id')->values(),
        ]);
    }

    public function updateProperty(Request $request, Property $property)
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'codigo_referencia' => ['nullable', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'tipo_propriedade_id' => ['required', 'integer', 'exists:property_types,id'],
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'valor' => ['required', 'string'],
            'endereco' => ['required', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', 'size:2'],
            'quartos' => ['nullable', 'integer', 'min:0'],
            'banheiros' => ['nullable', 'integer', 'min:0'],
            'garagens' => ['nullable', 'integer', 'min:0'],
            'show_in_home_selecao_especial' => ['nullable', 'boolean'],
            'show_in_home_mais_procurados' => ['nullable', 'boolean'],
            'show_in_home_visto_recentemente' => ['nullable', 'boolean'],
            'featured_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_photo_ids' => ['nullable', 'array'],
            'remove_photo_ids.*' => ['integer'],
            'photo_order_ids' => ['nullable', 'array'],
            'photo_order_ids.*' => ['integer'],
            'special_category_ids' => ['nullable', 'array'],
            'special_category_ids.*' => ['integer', 'exists:special_categories,id'],
        ]);

        $businessType = BusinessType::find($validated['business_type_id']);
        $valor = $this->parseBrlCurrency($validated['valor']);

        $codigoReferencia = $this->normalizeCodigoReferencia($validated['codigo_referencia'] ?? null);
        if ($codigoReferencia === '') {
            $codigoReferencia = $this->generateUniqueCodigoReferencia();
        }

        $property->fill([
            ...collect($validated)->except(['featured_image', 'gallery_images', 'special_category_ids', 'valor'])->all(),
            'codigo_referencia' => $codigoReferencia,
            'valor' => $valor,
            'operacao' => $this->mapBusinessTypeNameToLegacyOperacao($businessType?->name),
        ]);
        $property->save();

        $property->specialCategories()->sync($validated['special_category_ids'] ?? []);

        if ($request->hasFile('featured_image')) {
            $existingFeatured = $property->photos()->where('principal', true)->first();
            if ($existingFeatured) {
                Storage::disk('public')->delete($existingFeatured->arquivo);
                $existingFeatured->delete();
            }

            $featured = $request->file('featured_image');
            $path = Storage::disk('public')->putFile("properties/{$property->id}", $featured);
            $url = url('/storage/' . $path);
            PropertyPhoto::create([
                'property_id' => $property->id,
                'arquivo' => $path,
                'url' => $url,
                'principal' => true,
                'ordem' => 0,
            ]);
        }

        $removeIds = collect($validated['remove_photo_ids'] ?? [])
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        if ($removeIds->isNotEmpty()) {
            $photosToRemove = $property->photos()
                ->where('principal', false)
                ->whereIn('id', $removeIds->all())
                ->get();

            foreach ($photosToRemove as $photo) {
                Storage::disk('public')->delete($photo->arquivo);
                $photo->delete();
            }
        }

        $orderIds = collect($validated['photo_order_ids'] ?? [])
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        if ($orderIds->isNotEmpty()) {
            foreach ($orderIds as $idx => $photoId) {
                PropertyPhoto::query()
                    ->where('property_id', $property->id)
                    ->where('principal', false)
                    ->where('id', $photoId)
                    ->update(['ordem' => $idx + 1]);
            }
        }

        if ($request->hasFile('gallery_images')) {
            $currentMaxOrder = (int) ($property->photos()->max('ordem') ?? 0);
            $order = max(1, $currentMaxOrder + 1);

            foreach ($request->file('gallery_images', []) as $file) {
                $path = Storage::disk('public')->putFile("properties/{$property->id}", $file);
                $url = url('/storage/' . $path);
                PropertyPhoto::create([
                    'property_id' => $property->id,
                    'arquivo' => $path,
                    'url' => $url,
                    'principal' => false,
                    'ordem' => $order,
                ]);
                $order++;
            }
        }

        return Redirect::route('admin.properties');
    }

    public function destroyProperty(Property $property)
    {
        $property->load(['photos', 'specialCategories']);

        foreach ($property->photos as $photo) {
            Storage::disk('public')->delete($photo->arquivo);
        }

        $property->specialCategories()->detach();
        $property->delete();

        return Redirect::route('admin.properties');
    }

    private function parseBrlCurrency(string $input): float
    {
        $value = preg_replace('/[^\d,\.]/', '', $input);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }

    private function mapBusinessTypeNameToLegacyOperacao(?string $businessTypeName): string
    {
        if ($businessTypeName === 'Alugar') {
            return 'Aluguel';
        }

        if ($businessTypeName === 'Temporada') {
            return 'Temporada';
        }

        return 'Venda';
    }

    private function generateUniquePropertySlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 2;

        while (Property::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function generateUniqueCodigoAnuncio(): string
    {
        do {
            $codigo = strtoupper(Str::random(8));
        } while (Property::where('codigo_anuncio', $codigo)->exists());

        return $codigo;
    }

    private function normalizeCodigoReferencia(?string $input): string
    {
        $raw = strtoupper(trim((string) ($input ?? '')));
        if ($raw === '') {
            return '';
        }

        $raw = preg_replace('/[^A-Z0-9]/', '', $raw) ?? '';
        if ($raw === '') {
            return '';
        }

        if (strlen($raw) < 8) {
            return '';
        }

        return substr($raw, 0, 8);
    }

    private function generateUniqueCodigoReferencia(): string
    {
        do {
            $codigo = strtoupper(Str::random(8));
        } while (Property::where('codigo_referencia', $codigo)->exists());

        return $codigo;
    }
    
    public function leads(): Response
    {
        $leads = Lead::query()
            ->with(['property'])
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Admin/Leads', [
            'leads' => $leads,
        ]);
    }

    public function updateLead(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'max:60'],
            'proximo_contato_em' => ['nullable', 'date'],
        ]);

        $payload = [];
        if (array_key_exists('status', $validated) && $validated['status'] !== null) {
            $payload['status'] = $validated['status'];
        }
        if (array_key_exists('proximo_contato_em', $validated)) {
            $payload['proximo_contato_em'] = $validated['proximo_contato_em'];
        }

        if (count($payload)) {
            $lead->update($payload);
        }

        return Redirect::back();
    }
    
    public function appearance(): Response
    {
        $settings = Setting::all()->pluck('valor', 'chave');
        return Inertia::render('Admin/Appearance', ['settings' => $settings]);
    }

    public function updateAppearance(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'button_color' => ['nullable', 'string', 'max:20'],
            'footer_bg_color' => ['nullable', 'string', 'max:20'],
            'font_family' => ['nullable', 'string', 'max:255'],
            'font_size_text' => ['nullable', 'integer', 'min:10', 'max:24'],
            'font_size_title' => ['nullable', 'integer', 'min:18', 'max:72'],
            'home_hero_overlay_color' => ['nullable', 'string', 'max:20'],
            'home_hero_overlay_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'logo_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'favicon_file' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        foreach ([
            'primary_color',
            'secondary_color',
            'button_color',
            'footer_bg_color',
            'font_family',
            'font_size_text',
            'font_size_title',
            'home_hero_overlay_color',
            'home_hero_overlay_opacity',
        ] as $key) {
            if (!array_key_exists($key, $validated)) {
                continue;
            }

            Setting::updateOrCreate(
                ['chave' => $key],
                ['valor' => (string) ($validated[$key] ?? '')]
            );
        }

        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $path = Storage::disk('public')->putFile('branding/logo', $file);
            Setting::updateOrCreate(['chave' => 'logo_url'], ['valor' => url('/storage/' . $path)]);
        }

        if ($request->hasFile('favicon_file')) {
            $file = $request->file('favicon_file');
            $path = Storage::disk('public')->putFile('branding/favicon', $file);
            Setting::updateOrCreate(['chave' => 'favicon_url'], ['valor' => url('/storage/' . $path)]);
        }

        return Redirect::route('admin.appearance');
    }
    
    public function layout(): Response
    {
        $settings = Setting::all()->pluck('valor', 'chave');
        return Inertia::render('Admin/Layout', ['settings' => $settings]);
    }
    
    public function pages(): Response
    {
        $home = Page::firstOrNew(['slug' => 'home']);
        if (!$home->exists) {
            $home->fill([
                'titulo' => 'Home',
                'template' => 'home',
                'conteudo' => '',
                'banner_title' => 'Seja bem vindo! Seu novo lar está aqui.',
                'banner_subtitle' => '',
                'banner_title_color' => '#ffffff',
                'banner_subtitle_color' => '#ffffff',
                'banner_overlay_color' => '#0f172a',
                'banner_overlay_opacity' => 70,
                'ativo' => true,
            ]);
        } else {
            $home->template = 'home';
            $home->titulo = $home->titulo ?: 'Home';
            if ($home->banner_title === null || $home->banner_title === '') {
                $home->banner_title = 'Seja bem vindo! Seu novo lar está aqui.';
            }
        }
        $home->save();

        $about = Page::where('slug', 'quem-somos')->first();
        if (!$about) {
            $legacyAbout = Page::where('slug', 'sobre')->first();
            if ($legacyAbout) {
                $legacyAbout->slug = 'quem-somos';
                $about = $legacyAbout;
            }
        }
        if (!$about) {
            $about = new Page(['slug' => 'quem-somos']);
        }

        if (!$about->exists) {
            $about->fill([
                'titulo' => 'Quem Somos',
                'template' => 'about',
                'conteudo' => '',
                'ativo' => true,
                'data' => [
                    'hero_title_primary' => 'Conexão patrimonial.',
                    'hero_title_secondary' => 'Valor que permanece.',
                    'hero_subtitle' => 'Há mais de 12 anos conectando pessoas aos melhores endereços com expertise, confiança e sofisticação.',
                    'hero_button_label' => 'Explorar imóveis',
                    'hero_button_url' => '/imoveis',
                    'stats' => [
                        ['value' => '12+', 'label' => 'Anos de mercado'],
                        ['value' => '800+', 'label' => 'Imóveis negociados'],
                        ['value' => '2.350+', 'label' => 'Parceiros de negócios'],
                        ['value' => '100%', 'label' => 'Presença em Alphaville'],
                    ],
                    'essence' => [
                        'kicker' => 'Nossa essência',
                        'title_primary' => 'Construímos relações,',
                        'title_highlight' => 'não apenas negócios',
                        'text_1' => '',
                        'text_2' => '',
                        'bullets' => ['Alto padrão', 'Alphaville e região', 'Atendimento exclusivo'],
                        'badge_value' => '12',
                        'badge_label' => 'ANOS',
                    ],
                    'team' => [
                        'kicker' => 'Nosso time',
                        'title' => 'Quem faz acontecer',
                        'subtitle' => 'Profissionais apaixonados por conectar pessoas aos melhores endereços',
                        'members' => [
                            ['name' => 'Profissional 1', 'role' => 'Cargo', 'photo' => null],
                            ['name' => 'Profissional 2', 'role' => 'Cargo', 'photo' => null],
                        ],
                    ],
                    'quote' => [
                        'text' => 'Um endereço não é apenas um lugar. É onde a vida acontece, onde memórias são criadas, onde histórias começam.',
                        'author' => '',
                        'author_role' => '',
                    ],
                    'pillars' => [
                        ['title' => 'Confiança', 'description' => 'Transparência em cada etapa da negociação, com orientação real e objetiva.'],
                        ['title' => 'Conexão', 'description' => 'Entendemos o momento de cada cliente para sugerir o imóvel certo.'],
                        ['title' => 'Expertise', 'description' => 'Conhecimento de mercado, valorização e regiões para decisões seguras.'],
                        ['title' => 'Valor', 'description' => 'Atendimento cuidadoso e foco em longo prazo, não apenas na venda.'],
                    ],
                    'territory' => [
                        'kicker' => 'Nosso território',
                        'title' => 'Alphaville é',
                        'title_highlight' => 'nossa casa',
                        'text_1' => 'Conhecemos cada rua, cada condomínio, cada detalhe dessa região que amamos. Não somos apenas corretores — somos moradores, vizinhos, parte da comunidade.',
                        'text_2' => 'Essa proximidade nos permite oferecer insights reais sobre valorização, qualidade de vida e o potencial de cada imóvel.',
                        'regions' => ['Alphaville', 'Tamboré'],
                        'images' => ['main' => null, 'square' => null, 'wide' => null],
                    ],
                ],
            ]);
        } else {
            $about->template = 'about';
            $about->titulo = $about->titulo ?: 'Quem Somos';
            if ($about->slug !== 'quem-somos') {
                $about->slug = 'quem-somos';
            }
            if (empty($about->data) || !is_array($about->data)) {
                $about->data = [
                    'hero_title_primary' => 'Conexão patrimonial.',
                    'hero_title_secondary' => 'Valor que permanece.',
                    'hero_subtitle' => 'Há mais de 12 anos conectando pessoas aos melhores endereços com expertise, confiança e sofisticação.',
                    'hero_button_label' => 'Explorar imóveis',
                    'hero_button_url' => '/imoveis',
                    'stats' => [
                        ['value' => '12+', 'label' => 'Anos de mercado'],
                        ['value' => '800+', 'label' => 'Imóveis negociados'],
                        ['value' => '2.350+', 'label' => 'Parceiros de negócios'],
                        ['value' => '100%', 'label' => 'Presença em Alphaville'],
                    ],
                    'essence' => [
                        'kicker' => 'Nossa essência',
                        'title_primary' => 'Construímos relações,',
                        'title_highlight' => 'não apenas negócios',
                        'text_1' => '',
                        'text_2' => '',
                        'bullets' => ['Alto padrão', 'Alphaville e região', 'Atendimento exclusivo'],
                        'badge_value' => '12',
                        'badge_label' => 'ANOS',
                    ],
                    'team' => [
                        'kicker' => 'Nosso time',
                        'title' => 'Quem faz acontecer',
                        'subtitle' => 'Profissionais apaixonados por conectar pessoas aos melhores endereços',
                        'members' => [
                            ['name' => 'Profissional 1', 'role' => 'Cargo', 'photo' => null],
                            ['name' => 'Profissional 2', 'role' => 'Cargo', 'photo' => null],
                        ],
                    ],
                    'quote' => [
                        'text' => 'Um endereço não é apenas um lugar. É onde a vida acontece, onde memórias são criadas, onde histórias começam.',
                        'author' => '',
                        'author_role' => '',
                    ],
                    'pillars' => [
                        ['title' => 'Confiança', 'description' => 'Transparência em cada etapa da negociação, com orientação real e objetiva.'],
                        ['title' => 'Conexão', 'description' => 'Entendemos o momento de cada cliente para sugerir o imóvel certo.'],
                        ['title' => 'Expertise', 'description' => 'Conhecimento de mercado, valorização e regiões para decisões seguras.'],
                        ['title' => 'Valor', 'description' => 'Atendimento cuidadoso e foco em longo prazo, não apenas na venda.'],
                    ],
                    'territory' => [
                        'kicker' => 'Nosso território',
                        'title' => 'Alphaville é',
                        'title_highlight' => 'nossa casa',
                        'text_1' => 'Conhecemos cada rua, cada condomínio, cada detalhe dessa região que amamos. Não somos apenas corretores — somos moradores, vizinhos, parte da comunidade.',
                        'text_2' => 'Essa proximidade nos permite oferecer insights reais sobre valorização, qualidade de vida e o potencial de cada imóvel.',
                        'regions' => ['Alphaville', 'Tamboré'],
                        'images' => ['main' => null, 'square' => null, 'wide' => null],
                    ],
                ];
            }
        }

        $svg = function (string $label, int $w, int $h, string $c1, string $c2): string {
            $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $w . '" height="' . $h . '" viewBox="0 0 ' . $w . ' ' . $h . '">'
                . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
                . '<stop offset="0" stop-color="' . $c1 . '"/><stop offset="1" stop-color="' . $c2 . '"/></linearGradient></defs>'
                . '<rect width="' . $w . '" height="' . $h . '" fill="url(#g)"/>'
                . '<text x="' . (int) ($w / 2) . '" y="' . (int) ($h / 2) . '" text-anchor="middle" font-family="Arial, sans-serif" font-size="' . max(18, (int) ($w / 24)) . '" fill="rgba(255,255,255,0.55)">' . $safeLabel . '</text>'
                . '</svg>';
            return 'data:image/svg+xml,' . rawurlencode($svg);
        };

        $sampleAboutData = [
            'hero_title_primary' => 'Conexão patrimonial.',
            'hero_title_secondary' => 'Valor que permanece.',
            'hero_subtitle' => 'Especialistas em alto padrão, com curadoria, estratégia e atendimento humano do primeiro contato ao pós-venda.',
            'hero_button_label' => 'Explorar imóveis',
            'hero_button_url' => '/imoveis',
            'hero_background_image' => $svg('Banner (Quem Somos)', 1600, 900, '#0f172a', '#1e3a8a'),
            'stats' => [
                ['value' => '14+', 'label' => 'Anos de experiência'],
                ['value' => '1.240+', 'label' => 'Imóveis negociados'],
                ['value' => '86%', 'label' => 'Clientes por indicação'],
                ['value' => '28', 'label' => 'Bairros atendidos'],
            ],
            'essence' => [
                'kicker' => 'Nossa essência',
                'title_primary' => 'Construímos relações,',
                'title_highlight' => 'não apenas negócios',
                'text_1' => 'Cada imóvel tem uma história, e cada cliente tem um momento. Nossa missão é aproximar os dois com clareza, confiança e agilidade.',
                'text_2' => 'Usamos dados, experiência local e um olhar apurado para antecipar oportunidades e orientar decisões com segurança.',
                'bullets' => ['Curadoria premium', 'Atuação local', 'Atendimento exclusivo'],
                'badge_value' => '14',
                'badge_label' => 'ANOS',
                'image' => $svg('Imagem da Essência', 1400, 900, '#111827', '#334155'),
            ],
            'team' => [
                'kicker' => 'Nosso time',
                'title' => 'Quem faz acontecer',
                'subtitle' => 'Profissionais especialistas em negociação, marketing e relacionamento, focados em resultados e experiência.',
                'members' => [
                    ['name' => 'Marina Duarte', 'role' => 'Especialista em Alto Padrão', 'photo' => $svg('Marina', 600, 600, '#0b1220', '#1e293b')],
                    ['name' => 'Henrique Lima', 'role' => 'Consultor de Negócios', 'photo' => $svg('Henrique', 600, 600, '#0b1220', '#334155')],
                ],
            ],
            'quote' => [
                'text' => 'Um endereço não é apenas um lugar. É onde a vida acontece, onde memórias são criadas e onde histórias começam.',
                'author' => 'Equipe Conecta',
                'author_role' => 'Imobiliária',
            ],
            'pillars' => [
                ['title' => 'Confiança', 'description' => 'Transparência, comunicação direta e documentação acompanhada do início ao fim.', 'icon' => $svg('C', 128, 128, '#1e3a8a', '#0f172a')],
                ['title' => 'Conexão', 'description' => 'Entendemos necessidades reais para sugerir imóveis que fazem sentido.', 'icon' => $svg('Co', 128, 128, '#f97316', '#7c2d12')],
                ['title' => 'Expertise', 'description' => 'Leitura de mercado, precificação e estratégia de negociação.', 'icon' => $svg('E', 128, 128, '#0ea5e9', '#0f172a')],
                ['title' => 'Valor', 'description' => 'Atendimento cuidadoso, com foco em longo prazo e experiência impecável.', 'icon' => $svg('V', 128, 128, '#22c55e', '#064e3b')],
            ],
            'territory' => [
                'kicker' => 'Nosso território',
                'title' => 'Alphaville é',
                'title_highlight' => 'nossa casa',
                'text_1' => 'Conhecemos cada rua, cada condomínio e cada detalhe da região. Não somos apenas corretores — somos parte da comunidade.',
                'text_2' => 'Isso nos permite oferecer insights reais sobre valorização, mobilidade, lifestyle e o potencial de cada imóvel.',
                'regions' => ['Alphaville', 'Tamboré', 'Aldeia da Serra', 'Barueri'],
                'images' => [
                    'main' => $svg('Território (Vertical)', 900, 1200, '#0f172a', '#1e293b'),
                    'square' => $svg('Território (Quadrado)', 900, 900, '#111827', '#334155'),
                    'wide' => $svg('Território (Horizontal)', 1200, 900, '#0b1220', '#1e3a8a'),
                ],
            ],
        ];

        $fillEmpty = function ($current, $sample) use (&$fillEmpty) {
            if (is_array($sample)) {
                $out = is_array($current) ? $current : [];
                foreach ($sample as $k => $v) {
                    $out[$k] = $fillEmpty($out[$k] ?? null, $v);
                }
                return $out;
            }

            if ($current === null) {
                return $sample;
            }

            if (is_string($current) && trim($current) === '') {
                return $sample;
            }

            return $current;
        };

        $about->data = $fillEmpty($about->data ?? [], $sampleAboutData);
        $about->save();
        $this->syncMenuItemForPage($about);

        $contact = Page::firstOrNew(['slug' => 'contato']);
        if (!$contact->exists) {
            $contact->fill([
                'titulo' => 'Contato',
                'template' => 'contact',
                'conteudo' => '<h1>Contato</h1><p>Entre em contato conosco.</p>',
                'ativo' => true,
            ]);
        } else {
            $contact->template = 'contact';
            $contact->titulo = $contact->titulo ?: 'Contato';
        }
        $contact->save();
        $this->syncMenuItemForPage($contact);

        $privacy = Page::firstOrNew(['slug' => 'politicas-de-privacidade']);
        if (!$privacy->exists) {
            $privacy->fill([
                'titulo' => 'Políticas de Privacidade',
                'template' => 'default',
                'conteudo' => '<h1>Políticas de Privacidade</h1><p>Edite este conteúdo no painel administrativo.</p>',
                'ativo' => true,
            ]);
        } else {
            $privacy->template = $privacy->template ?: 'default';
            $privacy->titulo = $privacy->titulo ?: 'Políticas de Privacidade';
        }
        $privacy->save();
        $this->syncMenuItemForPage($privacy);

        $pages = Page::orderBy('titulo')->get();
        return Inertia::render('Admin/Pages', ['pages' => $pages]);
    }

    public function createPage(): Response
    {
        return Inertia::render('Admin/PageCreate');
    }

    public function storePage(Request $request)
    {
        $template = (string) ($request->input('template') ?? 'default');

        $rules = [
            'titulo' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:pages,slug'],
            'template' => ['nullable', 'string', 'max:50'],
            'conteudo' => ['nullable', 'string'],
            'data' => ['nullable', 'array'],
            'banner_title' => ['nullable', 'string', 'max:255'],
            'banner_subtitle' => ['nullable', 'string', 'max:500'],
            'banner_image' => ['nullable', 'string', 'max:500'],
            'banner_title_color' => ['nullable', 'string', 'max:20'],
            'banner_subtitle_color' => ['nullable', 'string', 'max:20'],
            'banner_overlay_color' => ['nullable', 'string', 'max:20'],
            'banner_overlay_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'banner_image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ];

        if ($template === 'default') {
            $rules['conteudo'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $page = Page::create([
            'titulo' => $validated['titulo'],
            'slug' => $validated['slug'],
            'template' => $validated['template'] ?? 'default',
            'conteudo' => $validated['conteudo'] ?? '',
            'data' => $validated['data'] ?? null,
            'banner_title' => $validated['banner_title'] ?? null,
            'banner_subtitle' => $validated['banner_subtitle'] ?? null,
            'banner_image' => $validated['banner_image'] ?? null,
            'banner_title_color' => $validated['banner_title_color'] ?? '#ffffff',
            'banner_subtitle_color' => $validated['banner_subtitle_color'] ?? '#ffffff',
            'banner_overlay_color' => $validated['banner_overlay_color'] ?? '#0f172a',
            'banner_overlay_opacity' => $validated['banner_overlay_opacity'] ?? 70,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'ativo' => $validated['ativo'] ?? true,
        ]);

        if ($request->hasFile('banner_image_file')) {
            $file = $request->file('banner_image_file');
            $path = Storage::disk('public')->putFile("pages/{$page->id}", $file);
            $page->update(['banner_image' => url('/storage/' . $path)]);
        }

        $this->syncMenuItemForPage($page);

        return Redirect::route('admin.pages');
    }
    
    public function editPage(Page $page): Response
    {
        return Inertia::render('Admin/PageEdit', ['page' => $page]);
    }

    public function updatePage(Request $request, Page $page)
    {
        $template = (string) ($request->input('template') ?? $page->template ?? 'default');

        $rules = [
            'titulo' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:pages,slug,' . $page->id],
            'template' => ['nullable', 'string', 'max:50'],
            'conteudo' => ['nullable', 'string'],
            'data' => ['nullable', 'array'],
            'banner_title' => ['nullable', 'string', 'max:255'],
            'banner_subtitle' => ['nullable', 'string', 'max:500'],
            'banner_image' => ['nullable', 'string', 'max:500'],
            'banner_title_color' => ['nullable', 'string', 'max:20'],
            'banner_subtitle_color' => ['nullable', 'string', 'max:20'],
            'banner_overlay_color' => ['nullable', 'string', 'max:20'],
            'banner_overlay_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'banner_image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ];

        if ($template === 'default') {
            $rules['conteudo'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $page->update([
            'titulo' => $validated['titulo'],
            'slug' => $validated['slug'],
            'template' => $validated['template'] ?? $page->template ?? 'default',
            'conteudo' => $validated['conteudo'] ?? $page->conteudo,
            'data' => $validated['data'] ?? $page->data,
            'banner_title' => $validated['banner_title'] ?? null,
            'banner_subtitle' => $validated['banner_subtitle'] ?? null,
            'banner_image' => $validated['banner_image'] ?? null,
            'banner_title_color' => $validated['banner_title_color'] ?? '#ffffff',
            'banner_subtitle_color' => $validated['banner_subtitle_color'] ?? '#ffffff',
            'banner_overlay_color' => $validated['banner_overlay_color'] ?? '#0f172a',
            'banner_overlay_opacity' => $validated['banner_overlay_opacity'] ?? 70,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'ativo' => $validated['ativo'] ?? false,
        ]);

        if ($request->hasFile('banner_image_file')) {
            $file = $request->file('banner_image_file');
            $path = Storage::disk('public')->putFile("pages/{$page->id}", $file);
            $page->update(['banner_image' => url('/storage/' . $path)]);
        }

        $this->syncMenuItemForPage($page);

        return Redirect::route('admin.pages.edit', ['page' => $page->id]);
    }

    public function destroyPage(Page $page)
    {
        if ($page->slug === 'home') {
            return Redirect::route('admin.pages');
        }

        MenuItem::where('url', $this->pageMenuUrl($page->slug))->delete();
        $page->delete();

        return Redirect::route('admin.pages');
    }

    public function duplicatePage(Page $page)
    {
        $slugBase = $page->slug . '-copia';
        $slug = $slugBase;
        $suffix = 2;

        while (Page::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $suffix;
            $suffix++;
        }

        $copy = Page::create([
            'titulo' => $page->titulo . ' (Cópia)',
            'slug' => $slug,
            'template' => $page->template ?? 'default',
            'conteudo' => $page->conteudo,
            'data' => $page->data,
            'banner_title' => $page->banner_title,
            'banner_subtitle' => $page->banner_subtitle,
            'banner_image' => $page->banner_image,
            'banner_title_color' => $page->banner_title_color,
            'banner_subtitle_color' => $page->banner_subtitle_color,
            'banner_overlay_color' => $page->banner_overlay_color,
            'banner_overlay_opacity' => $page->banner_overlay_opacity,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'ativo' => $page->ativo,
        ]);

        $this->syncMenuItemForPage($copy);

        return Redirect::route('admin.pages.edit', ['page' => $copy->id]);
    }

    public function uploadPageMedia(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:5120'],
        ]);

        $file = $validated['file'];
        $path = Storage::disk('public')->putFile("pages/{$page->id}/media", $file);

        return response()->json([
            'url' => url('/storage/' . $path),
        ]);
    }
    
    public function settings(): Response
    {
        $settings = Setting::all()->pluck('valor', 'chave');
        return Inertia::render('Admin/Settings', ['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'nome_empresa' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:100'],
            'email_contato' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:100'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
            'about_hero_title_primary' => ['nullable', 'string', 'max:255'],
            'about_hero_title_secondary' => ['nullable', 'string', 'max:255'],
            'about_hero_subtitle' => ['nullable', 'string', 'max:500'],
            'about_hero_button_label' => ['nullable', 'string', 'max:100'],
            'about_hero_button_url' => ['nullable', 'string', 'max:255'],
            'about_hero_background_image' => ['nullable', 'string', 'max:500'],
            'about_hero_background_image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'about_stat_1_value' => ['nullable', 'string', 'max:50'],
            'about_stat_1_label' => ['nullable', 'string', 'max:100'],
            'about_stat_2_value' => ['nullable', 'string', 'max:50'],
            'about_stat_2_label' => ['nullable', 'string', 'max:100'],
            'about_stat_3_value' => ['nullable', 'string', 'max:50'],
            'about_stat_3_label' => ['nullable', 'string', 'max:100'],
            'about_stat_4_value' => ['nullable', 'string', 'max:50'],
            'about_stat_4_label' => ['nullable', 'string', 'max:100'],
            'about_essence_kicker' => ['nullable', 'string', 'max:100'],
            'about_essence_title_primary' => ['nullable', 'string', 'max:255'],
            'about_essence_title_highlight' => ['nullable', 'string', 'max:255'],
            'about_essence_text_1' => ['nullable', 'string', 'max:2000'],
            'about_essence_text_2' => ['nullable', 'string', 'max:2000'],
            'about_essence_bullet_1' => ['nullable', 'string', 'max:100'],
            'about_essence_bullet_2' => ['nullable', 'string', 'max:100'],
            'about_essence_bullet_3' => ['nullable', 'string', 'max:100'],
            'about_essence_badge_value' => ['nullable', 'string', 'max:20'],
            'about_essence_badge_label' => ['nullable', 'string', 'max:20'],
            'about_essence_image' => ['nullable', 'string', 'max:500'],
            'about_essence_image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'about_team_kicker' => ['nullable', 'string', 'max:100'],
            'about_team_title' => ['nullable', 'string', 'max:255'],
            'about_team_subtitle' => ['nullable', 'string', 'max:255'],
            'about_team_member_1_name' => ['nullable', 'string', 'max:100'],
            'about_team_member_1_role' => ['nullable', 'string', 'max:100'],
            'about_team_member_1_photo' => ['nullable', 'string', 'max:500'],
            'about_team_member_1_photo_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'about_team_member_2_name' => ['nullable', 'string', 'max:100'],
            'about_team_member_2_role' => ['nullable', 'string', 'max:100'],
            'about_team_member_2_photo' => ['nullable', 'string', 'max:500'],
            'about_team_member_2_photo_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $fileMap = [
            'about_hero_background_image_file' => 'about_hero_background_image',
            'about_essence_image_file' => 'about_essence_image',
            'about_team_member_1_photo_file' => 'about_team_member_1_photo',
            'about_team_member_2_photo_file' => 'about_team_member_2_photo',
        ];

        foreach ($fileMap as $fileKey => $settingKey) {
            if (!$request->hasFile($fileKey)) {
                continue;
            }

            $file = $request->file($fileKey);
            $path = Storage::disk('public')->putFile('site/about', $file);
            $url = url('/storage/' . $path);
            Setting::updateOrCreate(['chave' => $settingKey], ['valor' => (string) $url]);
        }

        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $fileMap)) {
                continue;
            }

            Setting::updateOrCreate(
                ['chave' => $key],
                ['valor' => (string) ($value ?? '')]
            );
        }

        return Redirect::route('admin.settings');
    }

    public function instagram(): Response
    {
        $settings = Setting::query()->pluck('valor', 'chave');
        $feed = [];

        if (!empty($settings['instagram_feed_json'])) {
            $decoded = json_decode($settings['instagram_feed_json'], true);
            if (is_array($decoded)) {
                $feed = $decoded;
            }
        }

        return Inertia::render('Admin/Instagram', [
            'settings' => $settings,
            'instagramFeed' => $feed,
        ]);
    }

    public function updateInstagram(Request $request)
    {
        $validated = $request->validate([
            'instagram_username' => ['nullable', 'string', 'max:100'],
            'instagram_user_id' => ['nullable', 'string', 'max:100'],
            'instagram_access_token' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['chave' => $key],
                ['valor' => (string) ($value ?? '')]
            );
        }

        return Redirect::route('admin.instagram');
    }

    public function refreshInstagramFeed()
    {
        $settings = Setting::query()->pluck('valor', 'chave');
        $userId = $settings['instagram_user_id'] ?? null;
        $token = $settings['instagram_access_token'] ?? null;

        if (empty($userId) || empty($token)) {
            return Redirect::route('admin.instagram');
        }

        $url = "https://graph.instagram.com/{$userId}/media?fields=id,media_type,media_url,thumbnail_url,permalink,caption,timestamp&access_token={$token}";

        try {
            $response = file_get_contents($url);
            $payload = json_decode($response ?: '[]', true);
            $data = $payload['data'] ?? [];

            if (is_array($data)) {
                Setting::updateOrCreate(
                    ['chave' => 'instagram_feed_json'],
                    ['valor' => json_encode(array_slice($data, 0, 30))]
                );
                Setting::updateOrCreate(
                    ['chave' => 'instagram_last_refresh'],
                    ['valor' => now()->toDateTimeString()]
                );
            }
        } catch (\Throwable) {
            Setting::updateOrCreate(
                ['chave' => 'instagram_last_refresh'],
                ['valor' => now()->toDateTimeString()]
            );
        }

        return Redirect::route('admin.instagram');
    }

    private function syncMenuItemForPage(Page $page): void
    {
        $url = $this->pageMenuUrl($page->slug);

        if ($url === '/') {
            return;
        }

        if (!$page->ativo) {
            MenuItem::where('url', $url)->delete();
            return;
        }

        MenuItem::updateOrCreate(
            ['url' => $url],
            [
                'label' => $page->titulo,
                'icon' => 'tag',
                'url' => $url,
                'order' => 50,
                'is_active' => true,
            ]
        );
    }

    private function pageMenuUrl(string $slug): string
    {
        if ($slug === 'home') {
            return '/';
        }
        if ($slug === 'sobre' || $slug === 'quem-somos') {
            return '/quem-somos';
        }
        if ($slug === 'contato') {
            return '/contato';
        }

        return '/' . $slug;
    }

    public function businessTypes(): Response
    {
        $items = BusinessType::orderBy('sort_order')->orderBy('name')->get();

        return Inertia::render('Admin/BusinessTypes', [
            'items' => $items,
        ]);
    }

    public function storeBusinessType(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = $this->uniqueSlug($validated['name'], BusinessType::class);

        BusinessType::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return Redirect::route('admin.business-types');
    }

    public function updateBusinessType(Request $request, BusinessType $businessType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = $this->uniqueSlug($validated['name'], BusinessType::class, $businessType->id);

        $businessType->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'is_active' => $validated['is_active'] ?? $businessType->is_active,
            'sort_order' => $validated['sort_order'] ?? $businessType->sort_order,
        ]);

        return Redirect::route('admin.business-types');
    }

    public function destroyBusinessType(BusinessType $businessType)
    {
        $businessType->delete();

        return Redirect::route('admin.business-types');
    }

    public function propertyTypes(): Response
    {
        $items = PropertyType::orderBy('nome_tipo')->orderBy('nome_subtipo')->get();

        return Inertia::render('Admin/PropertyTypes', [
            'items' => $items,
        ]);
    }

    public function storePropertyType(Request $request)
    {
        $validated = $request->validate([
            'nome_tipo' => ['required', 'string', 'max:255'],
            'nome_subtipo' => ['nullable', 'string', 'max:255'],
        ]);

        $label = $validated['nome_subtipo'] ? ($validated['nome_tipo'] . ' ' . $validated['nome_subtipo']) : $validated['nome_tipo'];
        $slug = $this->uniqueSlug($label, PropertyType::class);

        PropertyType::create([
            'nome_tipo' => $validated['nome_tipo'],
            'nome_subtipo' => $validated['nome_subtipo'] ?? null,
            'slug' => $slug,
        ]);

        return Redirect::route('admin.property-types');
    }

    public function updatePropertyType(Request $request, PropertyType $propertyType)
    {
        $validated = $request->validate([
            'nome_tipo' => ['required', 'string', 'max:255'],
            'nome_subtipo' => ['nullable', 'string', 'max:255'],
        ]);

        $label = $validated['nome_subtipo'] ? ($validated['nome_tipo'] . ' ' . $validated['nome_subtipo']) : $validated['nome_tipo'];
        $slug = $this->uniqueSlug($label, PropertyType::class, $propertyType->id);

        $propertyType->update([
            'nome_tipo' => $validated['nome_tipo'],
            'nome_subtipo' => $validated['nome_subtipo'] ?? null,
            'slug' => $slug,
        ]);

        return Redirect::route('admin.property-types');
    }

    public function destroyPropertyType(PropertyType $propertyType)
    {
        $propertyType->delete();

        return Redirect::route('admin.property-types');
    }

    public function specialCategories(): Response
    {
        $items = SpecialCategory::orderBy('sort_order')->orderBy('name')->get();

        return Inertia::render('Admin/SpecialCategories', [
            'items' => $items,
        ]);
    }

    public function storeSpecialCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = $this->uniqueSlug($validated['name'], SpecialCategory::class);

        SpecialCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return Redirect::route('admin.special-categories');
    }

    public function updateSpecialCategory(Request $request, SpecialCategory $specialCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = $this->uniqueSlug($validated['name'], SpecialCategory::class, $specialCategory->id);

        $specialCategory->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? $specialCategory->is_active,
            'sort_order' => $validated['sort_order'] ?? $specialCategory->sort_order,
        ]);

        return Redirect::route('admin.special-categories');
    }

    public function destroySpecialCategory(SpecialCategory $specialCategory)
    {
        $specialCategory->delete();

        return Redirect::route('admin.special-categories');
    }

    public function blogCategories(): Response
    {
        $items = BlogCategory::orderBy('name')->get();

        return Inertia::render('Admin/Blog/Categories', [
            'items' => $items,
        ]);
    }

    public function storeBlogCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $slug = $this->uniqueSlug($validated['name'], BlogCategory::class);

        BlogCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return Redirect::route('admin.blog.categories');
    }

    public function updateBlogCategory(Request $request, BlogCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $slug = $this->uniqueSlug($validated['name'], BlogCategory::class, $category->id);

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return Redirect::route('admin.blog.categories');
    }

    public function destroyBlogCategory(BlogCategory $category)
    {
        $category->delete();

        return Redirect::route('admin.blog.categories');
    }

    public function blogPosts(): Response
    {
        $posts = BlogPost::with('category')->orderByDesc('created_at')->get();
        $categories = BlogCategory::orderBy('name')->get();

        return Inertia::render('Admin/Blog/Posts', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function createBlogPost(): Response
    {
        $categories = BlogCategory::orderBy('name')->get();

        return Inertia::render('Admin/Blog/PostCreate', [
            'categories' => $categories,
        ]);
    }

    public function storeBlogPost(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = $this->uniqueSlug($validated['title'], BlogPost::class);

        $post = BlogPost::create([
            ...collect($validated)->except(['featured_image'])->all(),
            'slug' => $slug,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $path = Storage::disk('public')->putFile("blog/{$post->id}", $file);
            $post->update([
                'featured_image' => url('/storage/' . $path),
            ]);
        }

        return Redirect::route('admin.blog.posts');
    }

    public function editBlogPost(BlogPost $post): Response
    {
        $categories = BlogCategory::orderBy('name')->get();

        return Inertia::render('Admin/Blog/PostEdit', [
            'post' => $post->load('category'),
            'categories' => $categories,
        ]);
    }

    public function updateBlogPost(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = $this->uniqueSlug($validated['title'], BlogPost::class, $post->id);

        $post->update([
            ...collect($validated)->except(['featured_image'])->all(),
            'slug' => $slug,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $path = Storage::disk('public')->putFile("blog/{$post->id}", $file);
            $post->update([
                'featured_image' => url('/storage/' . $path),
            ]);
        }

        return Redirect::route('admin.blog.posts');
    }

    public function destroyBlogPost(BlogPost $post)
    {
        $post->delete();

        return Redirect::route('admin.blog.posts');
    }

    private function uniqueSlug(string $value, string $modelClass, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $suffix = 2;

        while (true) {
            $query = $modelClass::where('slug', $slug);

            if ($ignoreId !== null) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $slug = $base . '-' . $suffix;
            $suffix++;
        }
    }
}
