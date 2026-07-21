<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RecordStatus;
use App\Http\Controllers\Controller;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class CarModelController extends Controller
{
    public function index(Request $request): View
    {
        $models = CarModel::query()
            ->with('brand')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.car-models.index', compact('models'));
    }

    public function create(): View
    {
        return view('admin.car-models.create', $this->formData(new CarModel()));
    }

    public function store(Request $request): RedirectResponse
    {
        CarModel::query()->create($this->data($request));

        return redirect()->route('admin.car-models.index')->with('success', 'Car model created successfully.');
    }

    public function edit(CarModel $model): View
    {
        return view('admin.car-models.edit', $this->formData($model));
    }

    public function update(Request $request, CarModel $model): RedirectResponse
    {
        $model->update($this->data($request));

        return redirect()->route('admin.car-models.index')->with('success', 'Car model updated successfully.');
    }

    public function destroy(CarModel $model): RedirectResponse
    {
        $model->delete();

        return redirect()->route('admin.car-models.index')->with('success', 'Car model deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(CarModel $model): array
    {
        return [
            'model' => $model,
            'brands' => CarBrand::query()->orderBy('name')->get(),
            'statuses' => RecordStatus::cases(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function data(Request $request): array
    {
        return $request->validate([
            'car_brand_id' => ['required', 'integer', 'exists:car_brands,id'],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', new Enum(RecordStatus::class)],
        ]);
    }
}
