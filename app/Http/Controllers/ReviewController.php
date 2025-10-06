<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        try {
            $validated = $request->safe()->all();
            $validated['user_id'] = Auth::user()->id;

            $review = Review::create($validated);

            if($review) {
                return Response::json([
                    'message' => 'Review berhasil dibuat',
                    'data' => $review
                ], 201);
            }

            return Response::json([
                'message' => 'Review gagal dibuat',
                'data' => []
            ], 500);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        try {
            if($review->delete()) {
                return Response::json([
                    'message' => 'Review berhasil dihapus',
                    'data' => []
                ], 200);
            }

            return Response::json([
                'message' => 'Review gagal dihapus',
                'data' => []
            ], 500);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
