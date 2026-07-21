<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RecordStatus;
use App\Http\Controllers\Controller;
use App\Models\CarBrand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class CarBrandController extends Controller
{
    public function index(Request $request): View
    {
        $brands = CarBrand::query()
            ->withCount('models')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.car-brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.car-brands.create', ['brand' => new CarBrand(), 'statuses' => RecordStatus::cases()]);
    }

    public function store(Request $request): RedirectResponse
    {
        CarBrand::query()->create($this->data($request));

        return redirect()->route('admin.car-brands.index')->with('success', 'Car brand created successfully.');
    }

    public function edit(CarBrand $brand): View
    {
        return view('admin.car-brands.edit', ['brand' => $brand, 'statuses' => RecordStatus::cases()]);
    }

    public function update(Request $request, CarBrand $brand): RedirectResponse
    {
        $brand->update($this->data($request, $brand));

        return redirect()->route('admin.car-brands.index')->with('success', 'Car brand updated successfully.');
    }

    public function destroy(CarBrand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('admin.car-brands.index')->with('success', 'Car brand deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function data(Request $request, ?CarBrand $brand = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'status' => ['required', new Enum(RecordStatus::class)],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('car-brands', 'public');
        } elseif ($brand) {
            unset($data['logo']);
        }

        return $data;
    }
}
