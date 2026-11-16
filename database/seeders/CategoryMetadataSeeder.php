<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryMetadataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metadataTec = Category::first()->metadata()->create([
            'id' => 'tecnologia',
            'name' => 'Tecnologia',
        ]);

        $metadataCor = Category::first()->metadata()->create([
            'id' => 'cor',
            'name' => 'Cor',
        ]);

        $metadataTec->values()->create([
            'id' => 'laravel',
            'label' => 'Laravel',
        ]);

        $metadataTec->values()->create([
            'id' => 'react-native',
            'label' => 'React Native',
        ]);

        $metadataCor->values()->create([
            'id' => 'preto',
            'label' => 'Preto',
        ]);

        $metadataCor->values()->create([
            'id' => 'azul',
            'label' => 'Azul',
        ]);

        Product::first()->metadata()->create([
            'category_metadata_id' => 'cor',
            'metadata_value_id' => 'azul',
        ]);

        Product::first()->metadata()->create([
            'category_metadata_id' => 'tecnologia',
            'metadata_value_id' => 'laravel',
        ]);
    }
}
