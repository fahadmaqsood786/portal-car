<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
    {
        $faker = FakerFactory::create();

        return [
            'user_id' => 3,
            'job_type_id' => rand(1, 5),
            'category_id' => rand(1, 5),
            'vacancy' => rand(1, 5),
            'title' => $faker->sentence, // Add this line
            'location' => $faker->city,
            'description' => $faker->text,
            'experience' => rand(1, 3),
            'company_name' => $faker->name,
        ];
    }
}
