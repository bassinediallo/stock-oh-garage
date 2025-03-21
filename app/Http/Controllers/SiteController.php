<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sites = Site::withCount(['departments', 'users'])->get();
        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        $this->authorize('create', Site::class);
        return view('sites.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Site::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        Site::create($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site créé avec succès.');
    }

    public function show(Site $site)
    {
        $departments = $site->departments()->withCount('stocks')->get();
        $users = $site->users;
        
        return view('sites.show', compact('site', 'departments', 'users'));
    }

    public function edit(Site $site)
    {
        $this->authorize('update', $site);
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $this->authorize('update', $site);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $site->update($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site mis à jour avec succès.');
    }

    public function destroy(Site $site)
    {
        $this->authorize('delete', $site);
        
        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site supprimé avec succès.');
    }
}
