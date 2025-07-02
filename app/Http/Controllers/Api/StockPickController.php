<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\StockPick;

class StockPickController extends Controller
{
    public function index()
    {
        return response()->json(StockPick::with('newsletter:id,slug,mediaType')->get());
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'symbol' => 'required|string|max:10',
            'newsletter_id' => 'nullable|exists:newsletter,id',
            'recommendation_date' => 'required|date',
            'initial_price' => 'required|numeric',
            'current_price' => 'nullable|numeric'
        ]);

        $stockPick = StockPick::create($validated);
        return response()->json(StockPick::with('newsletter:id,slug,mediaType')->find($stockPick->id), 201);
    }

    public function update(Request $request, StockPick $stockPick)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'symbol' => 'sometimes|string|max:10',
            'newsletter_id' => 'nullable|exists:newsletter,id',
            'recommendation_date' => 'sometimes|date',
            'initial_price' => 'sometimes|numeric',
            'current_price' => 'sometimes|numeric'
        ]);

        $stockPick->update($validated);
        return response()->json(StockPick::with('newsletter:id,slug,mediaType')->find($stockPick->id));
    }

    public function updatePrice(Request $request, StockPick $stockPick)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'current_price' => 'required|numeric'
        ]);

        $stockPick->update(['current_price' => $validated['current_price']]);
        return response()->json(StockPick::with('newsletter:id,slug,mediaType')->find($stockPick->id));
    }

    public function batchUpdatePrices(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:stock_picks,id',
            'updates.*.current_price' => 'required|numeric'
        ]);

        $updated = [];
        foreach ($validated['updates'] as $update) {
            $stockPick = StockPick::find($update['id']);
            $stockPick->update(['current_price' => $update['current_price']]);
            $updated[] = StockPick::with('newsletter:id,slug,mediaType')->find($stockPick->id);
        }

        return response()->json($updated);
    }
}
