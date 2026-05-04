<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateVariantSizeRequest;
use App\Services\VariantSizeService;
use App\Http\Requests\StoreVariantSizeRequest;

class VariantSizeController extends Controller
{
  protected VariantSizeService $service;

    public function __construct(VariantSizeService $service)
    {
        $this->service = $service;
    }

    public function update(UpdateVariantSizeRequest $request, $id)
    {
        $size = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $size
        ]);
    }

    public function store(StoreVariantSizeRequest $request)
    {
        $size = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $size
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Size silindi'
        ]);
    }
}
