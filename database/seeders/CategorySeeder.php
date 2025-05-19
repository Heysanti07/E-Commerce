<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();
        Department::truncate();

        $products = collect(Storage::json('data/products_with_images.json'));

        // Crear departamentos
        $departments = $products->pluck('department')->unique()->mapWithKeys(function ($item) {
            $slug = Str::slug($item);
            $department = Department::create([
                'name' => $item,
                'slug' => $slug,
                'img' => "/img/departments/$slug.png",
            ]);
            return [$item => $department->id];
        });

        // Crear categorías
        $products->unique('category')->each(function ($item) use ($departments) {
            $slug = Str::slug($item['category']);
            Category::create([
                'name' => $item['category'],
                'slug' => $slug,
                'img' => "img/categories/$slug.png",
                'department_id' => $departments[$item['department']],
            ]);
        });

        // Categorías tipo blog
        Category::factory()->count(5)->create([
            'type' => 'blog'
        ]);
    }
}