<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = array_keys(config('school.news.categories', ['pengumuman' => []]));
        $title = Str::title(trim(fake()->unique()->sentence(7), '.'));

        return [
            'slug' => Str::slug($title),
            'title' => $title,
            'excerpt' => fake()->sentence(14),
            'body' => implode("\n\n", [
                fake()->paragraph(3),
                fake()->paragraph(4),
                fake()->paragraph(3),
            ]),
            'category' => fake()->randomElement($categories),
            'image' => null,
            'gallery' => null,
            'video' => null,
            'author' => fake()->randomElement(['Humas SMK Islamiyah Ciputat', 'Tim Redaksi Sekolah', null]),
            'views' => fake()->numberBetween(12, 900),
            'published_at' => fake()->dateTimeBetween('-18 months'),
            'is_featured' => false,
            'is_picked' => false,
        ];
    }

    /** A headline for the home page's lead story. */
    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    /** An entry in the curated picks slider. */
    public function picked(): static
    {
        return $this->state(fn () => ['is_picked' => true]);
    }
}
