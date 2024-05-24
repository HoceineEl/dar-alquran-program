<?php

namespace Database\Seeders;

use App\Models\Ayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AyahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load JSON data
        $jsonPath = database_path('seeders/warshData_v2-1.json');
        $jsonData = File::get($jsonPath);
        $ayahs = json_decode($jsonData, true);

        // Insert data into the database
        foreach ($ayahs as $ayahData) {
            Ayah::create([
                'id' => $ayahData['id'],
                'page_number' => $ayahData['page'],
                'line_start' => $ayahData['line_start'],
                'jozz' => $ayahData['jozz'],
                'line_end' => $ayahData['line_end'],
                'surah_name' => $ayahData['sura_name_ar'],
                'ayah_text' => $ayahData['aya_text'],
                'ayah_no' => $ayahData['aya_no'],
                'lines_count' => 0, // Placeholder, will be updated later
            ]);
        }

        // Calculate lines_count for each page and update the records
        $pages = Ayah::select('page_number')->distinct()->get();
        foreach ($pages as $page) {
            $maxLine = Ayah::where('page_number', $page->page_number)->max('line_end');
            // Check if the page contains ayah_no = 1
            $containsAyah1 = Ayah::where('page_number', $page->page_number)->where('ayah_no', 1)->exists();
            if ($containsAyah1) {
                $maxLine -= 2;
            }
            $linesCount = $maxLine;
            Ayah::where('page_number', $page->page_number)->update(['lines_count' => $linesCount]);
        }
    }
}
