<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

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

    public function finish(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'cart' => ['required', 'array', 'min:1'],
                'cart.*.productId' => ['required', 'numeric', 'min:1'],
                'cart.*.quantity' => ['required', 'numeric', 'min:1'],
                'addressId' => ['required', 'numeric', 'min:1'],
            ],
            [
                'cart.required' => 'O campo cart é obrigatório.',
                'cart.array' => 'O campo cart deve ser um array.',
                'cart.min' => 'O campo cart deve ter ao menos 1 item.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'products' => []
            ], 400);
        }

        $user = Auth::user();

        $address = Address::find($request->input('addressId'));

        if ($address->user_id != $user->id) {
            return response()->json([
                'error' => 'Endereço não pertence ao usuário',
                'products' => []
            ]);
        }

        $cart = $request->input('cart');

        $products = Product::whereIn('id', array_column($cart, 'productId'))->get();

        $total = 0;
        foreach($cart as $item) {
            $product = $products->find($item['productId']);
            $total += $product->price * $item['quantity'];
        }

        dd($total);
    }
}
