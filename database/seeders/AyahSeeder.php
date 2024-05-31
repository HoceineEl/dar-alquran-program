<?php

namespace Database\Seeders;

use App\Models\Ayah;
use App\Models\Page;
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

        // Group ayahs by page number
        $ayahsByPage = collect($ayahs)->groupBy('page');

        // Create pages and ayahs
        $nexPageMinOne = false;
        foreach ($ayahsByPage as $pageNumber => $ayahs) {
            // Calculate lines_count for the page
            $maxLine = collect($ayahs)->max('line_end');
            $ayah1Count = collect($ayahs)->where('aya_no', 1)->count();
            if ($nexPageMinOne) {
                $linesCount = $maxLine - 1;
                $ayah1Count -= 1;
            }
            $linesCount = $maxLine - (2 * $ayah1Count);
            if ($maxLine == 15) {
                $nexPageMinOne = false;
            } else if ($maxLine == 14) {
                $nexPageMinOne = true;
            }


            // Create the Page record
            $page = Page::create([
                'number' => $pageNumber,
                'surah_name' => $ayahs[0]['sura_name_ar'],
                'lines_count' => $linesCount,
                'jozz' => $ayahs[0]['jozz'],
            ]);

            // Create the Ayah records
            foreach ($ayahs as $ayahData) {
                Ayah::create([
                    'page_id' => $page->id,
                    'line_start' => $ayahData['line_start'],
                    'line_end' => $ayahData['line_end'],
                    'ayah_text' => $ayahData['aya_text'],
                    'ayah_no' => $ayahData['aya_no'],
                ]);
            }
        }
    }
}
