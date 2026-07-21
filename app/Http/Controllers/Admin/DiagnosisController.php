<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DiagnosisConfidence;
use App\Enums\DiagnosisStatus;
use App\Enums\DiagnosisUrgency;
use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiagnosisController extends Controller
{
    public function index(Request $request): View
    {
        $diagnoses = Diagnosis::query()
            ->with(['user', 'vehicle.brand', 'vehicle.model', 'affectedCategory'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('urgency'), fn ($q) => $q->where('urgency', $request->string('urgency')))
            ->when($request->filled('confidence'), fn ($q) => $q->where('confidence', $request->string('confidence')))
            ->when($request->filled('affected_category'), fn ($q) => $q->where('affected_category_id', $request->integer('affected_category')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.diagnoses.index', [
            'diagnoses' => $diagnoses,
            'statuses' => DiagnosisStatus::cases(),
            'urgencies' => DiagnosisUrgency::cases(),
            'confidences' => DiagnosisConfidence::cases(),
            'categories' => ServiceCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function show(Diagnosis $diagnosis): View
    {
        return view('admin.diagnoses.show', [
            'diagnosis' => $diagnosis->load(['user', 'vehicle.brand', 'vehicle.model', 'affectedCategory', 'media', 'suggestions.workshop']),
        ]);
    }

    public function manualReview(Diagnosis $diagnosis): RedirectResponse
    {
        $diagnosis->update(['status' => DiagnosisStatus::ManualReview]);

        return back()->with('success', 'Diagnosis marked for manual review.');
    }

    public function complete(Diagnosis $diagnosis): RedirectResponse
    {
        $diagnosis->update(['status' => DiagnosisStatus::Completed]);

        return back()->with('success', 'Diagnosis marked completed.');
    }
}
