<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopVerificationController extends Controller
{
    public function index(): View
    {
        return view('admin.workshop-verifications.index', [
            'verifications' => WorkshopVerification::query()->with('workshop')->latest('id')->paginate(15),
        ]);
    }

    public function show(WorkshopVerification $verification): View
    {
        return view('admin.workshop-verifications.show', ['verification' => $verification->load(['workshop', 'verifier'])]);
    }

    public function approve(Request $request, WorkshopVerification $verification): RedirectResponse
    {
        $verification->update([
            'status' => 'approved',
            'admin_notes' => $request->input('admin_notes'),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);
        $verification->workshop()->update(['is_verified' => true]);

        return back()->with('success', 'Verification approved.');
    }

    public function reject(Request $request, WorkshopVerification $verification): RedirectResponse
    {
        $verification->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes'),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Verification rejected.');
    }
}
