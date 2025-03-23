<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklists = Checklist::orderBy('date_verification', 'desc')->paginate(10);
        return view('checklists.index', compact('checklists'));
    }

    public function create()
    {
        return view('checklists.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_verification' => 'required|date',
            // Les autres champs sont optionnels
        ]);

        Checklist::create($request->all());

        return redirect()->route('checklists.index')
            ->with('success', 'Checklist mensuelle créée avec succès.');
    }

    public function show(Checklist $checklist)
    {
        return view('checklists.show', compact('checklist'));
    }

    public function edit(Checklist $checklist)
    {
        return view('checklists.edit', compact('checklist'));
    }

    public function update(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'date_verification' => 'required|date',
            // Les autres champs sont optionnels
        ]);

        $checklist->update($request->all());

        return redirect()->route('checklists.index')
            ->with('success', 'Checklist mensuelle mise à jour avec succès.');
    }

    public function destroy(Checklist $checklist)
    {
        $checklist->delete();

        return redirect()->route('checklists.index')
            ->with('success', 'Checklist mensuelle supprimée avec succès.');
    }
}
