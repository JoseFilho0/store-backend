<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategoryMetadataBySlug(Request $request, $slug)
    {
        if (empty($slug) || !is_string($slug)) {
            return response()->json([
                'error' => 'Slug inválido.',
                'category' => null,
                'metadata' => []
            ], 400);
        }

        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return response()->json([
                'error' => 'Categoria não encontrada.',
                'category' => null,
                'metadata' => []
            ], 404);
        }

        $rawMetadata = $category->metadata()->get();

        $formattedMetadata = $rawMetadata->map(function ($metadata) {
            return [
                'id' => $metadata->id,
                'name' => $metadata->name,
                'values' => $metadata->values->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value
                    ];
                })
            ];
        });

        return response()->json([
            'error' => null,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug
            ],
            'metadata' => $formattedMetadata
        ]);
    }
}
