<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function mount(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'ids' => ['required', 'array', 'min:1'],
                'ids.*' => ['numeric', 'min:1'],
            ],
            [
                'ids.required' => 'O campo id é obrigatório.',
                'ids.array' => 'O campo ids deve ser um array.',
                'ids.min' => 'O campo ids deve conter pelo menos 1 item.',
                'ids.*.numeric' => 'Todos os itens do campo ids devem ser números.',
                'ids.*.min' => 'Todos os itens do campo ids devem ser maiores que 0.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'products' => []
            ], 400);
        }

        $ids = $request->input('ids');

        $products = Product::with('images')->whereIn('id', $ids)->get();

        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'label' => $product->label,
                'price' => $product->price,
                'image' => asset('storage/' . ($product->images->first()->url ?? 'images/default.png')),
            ];
        });

        return response()->json([
            'error' => null,
            'products' => $formattedProducts
        ]);
    }

    public function shipping(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'zipcode' => ['required', 'min:4'],
            ],
            [
                'zipcode.required' => 'O campo zipcode é obrigatório.',
                'zipcode.min' => 'O campo zipcode deve ter pelo menos 4 caracteres.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'shipping' => null
            ], 400);
        }

        return response()->json([
            'error' => null,
            'shipping' => [
                'zipcode' => $request->input('zipcode'),
                'price' => 7,
                'days' => 3
            ]
        ]);
    }
}
