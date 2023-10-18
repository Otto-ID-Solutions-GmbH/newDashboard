<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CintasDusterTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedTags('import/duster');
    }

    private function seedTags($dir): void
    {
        foreach (Storage::disk('local')->allFiles($dir) as $filename) {
            Excel::import(new \Cintas\Imports\TagDataImport(), $filename, 'local');
        }
    }
}
