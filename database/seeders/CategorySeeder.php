<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $categories = [
            'Quran',
            'Hadith',
            'Fiqh',
            'Tafsir',
            'Seerah',
            'Aqidah',
            'Dua & Dhikr',
            'Islamic History',
            'Halal & Haram',
            'Islamic Ethics',
            'Islamic Studies',
            'Islamic Philosophy',
            'Islamic Finance',
            'Islamic Art',
            'Islamic Culture',
            'Islamic Science',
            'Islamic Education',
            'Islamic Literature',
            'Islamic Poetry',
            'Islamic Politics'
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }
    }

}
