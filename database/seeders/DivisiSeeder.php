<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    public function run(): void
    {
        $divisis = [
            ['nama_divisi' => 'IT Support', 'kuota' => 5],
            ['nama_divisi' => 'Finance', 'kuota' => 5],
            ['nama_divisi' => 'HRD', 'kuota' => 5],
        ];

        foreach ($divisis as $divisi) {
            Divisi::create($divisi);
        }
    }
}
