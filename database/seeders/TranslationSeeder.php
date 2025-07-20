<?php

namespace Database\Seeders;

use App\Models\Translation;
use Database\Factories\TranslationFactory;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Translation::truncate();
        $total = 100000;
        $chunkSize = 1000;

        $this->command->info("Seeding {$total} translations using batch inserts...");

        $this->command->getOutput()->progressStart($total);

        for ($i = 0; $i < $total; $i += $chunkSize) {
            TranslationFactory::factoryForModel(Translation::class)->count($chunkSize)->create();

            $this->command->getOutput()->progressAdvance($chunkSize);
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info("Done seeding translations!");

    }
}
