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

        // Insert data into the database
        foreach ($ayahs as $ayahData) {
            Ayah::create([
                'id' => $ayahData['id'],
                'page_number' => $ayahData['page'],
                'line_start' => $ayahData['line_start'],
                'line_end' => $ayahData['line_end'],
                'surah_name' => $ayahData['sura_name_ar'],
                'ayah_text' => $ayahData['aya_text'],
                'ayah_no' => $ayahData['aya_no'],
            ]);
        }

        // Fetch all ayahs grouped by page number
        $ayahsByPage = Ayah::all()->groupBy('page_number');

        // Calculate lines_count for each page and create/update Page records
        foreach ($ayahsByPage as $pageNumber => $ayahs) {
            $maxLine = $ayahs->max('line_end');
            // Count the number of ayah_no = 1 on this page
            $ayah1Count = $ayahs->where('ayah_no', 1)->count();
            $linesCount = $maxLine - (2 * $ayah1Count);

            // Create or update the Page record
            $page = Page::updateOrCreate(
                ['number' => $pageNumber],
                [
                    'surah_name' => $ayahs->first()->surah_name,
                    'lines_count' => $linesCount,
                    'jozz' => $ayahs->first()->jozz,
                ]
            );

            // Update the page_id for each Ayah record
            Ayah::where('page_number', $pageNumber)->update(['page_id' => $page->id]);
        }
    }
}
