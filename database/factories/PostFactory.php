<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use App\User;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'caption' => $faker->title,
        'image' => 'images/picture.jpg',
        'user_id' => factory(User::class),

    ];
});
