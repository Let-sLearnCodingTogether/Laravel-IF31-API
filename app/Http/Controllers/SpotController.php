<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Http\Requests\UpdateSpotRequest;
use App\Models\Category;
use App\Models\Spot;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class SpotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $spots = Spot::with([
                'categories:category,spot_id',
                'user:id,name'
            ])
                ->withCount([
                    'reviews'
                ])
                ->withSum('reviews', 'rating')
                ->orderBy('created_at', 'desc')
                ->paginate(request('size', 10));

            return Response::json([
                'message' => "Spots",
                'data' => $spots
            ], 200);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpotRequest $request)
    {
        try {
            $validated = $request->safe()->all();

            $picture_path = Storage::disk('public')->putFile('spots', $request->file('picture'));

            $spot = Spot::create([
                'user_id' => Auth::user()->id,
                'name' => $validated['name'],
                'address' => $validated['address'],
                'picture' => $picture_path,
            ]);

            if ($spot) {
                $categories = [];

                foreach ($validated['category'] as $category) {
                    $categories[] = [
                        'spot_id' => $spot->id,
                        'category' => $category
                    ];
                }

                Category::fillAndInsert($categories);

                return Response::json([
                    'message' => 'Spot created successfully',
                    'data' => $spot
                ], 201);
            }

            return Response::json([
                'message' => 'Spot not created',
                'data' => null
            ], 500);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Spot $spot)
    {
        try {
            return Response::json([
                'message' => 'Informasi Spot',
                'data' => $spot->load([
                    'categories:category,spot_id',
                    'user:id,name',
                    'reviews' => function ($query) {
                        $query->with('user:id,name');
                    }
                ])
                    ->loadCount([
                        'reviews'
                    ])
                    ->loadSum('reviews', 'rating')
            ]);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpotRequest $request, Spot $spot)
    {
        try {
            $validated = $request->safe()->all();

            if (isset($validated['picture'])) {
                $picture_path = Storage::disk('public')->putFile('spots', $request->file('picture'));
            }

            if (isset($validated['category'])) {
                Category::where('spot_id', $spot->id)->delete();

                $categories = [];

                foreach ($validated['category'] as $category) {
                    $categories[] = [
                        'spot_id' => $spot->id,
                        'category' => $category
                    ];
                }

                Category::fillAndInsert($categories);
            }

            $spot->update([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'picture' => $picture_path ?? $spot->picture
            ]);

            return Response::json([
                'message' => 'Spot updated successfully',
                'data' => $spot
            ], 200);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spot $spot)
    {
        try {
            $user = Auth::user();

            if ($spot->user_id != $user->id && $user->role != "ADMIN") {
                return Response::json([
                    'message' => 'Spot gagal di hapus',
                    'data' => null
                ], 403);
            } else if ($spot->delete()) {
                return Response::json([
                    'message' => 'Spot berhasil di hapus',
                    'data' => null
                ], 200);
            }

            return Response::json([
                'message' => 'Spot gagal di hapus',
                'data' => null
            ], 500);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}