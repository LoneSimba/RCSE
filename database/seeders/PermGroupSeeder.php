<?php

namespace Database\Seeders;

use App\Models\PermGroup;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $baseGroups = [
            [
                'slug' => 'admin',
            ],
            [
                'slug' => 'user',
            ],
            [
                'slug' => 'banished',
            ]
        ];

        $relatedGroups = [
            [
                'slug' => 'confined',
            ],
            [
                'slug' => 'moderator',
            ]
        ];

        $models = [];
        $curDate = (new PermGroup)->freshTimestamp()->toDateTimeString();
        foreach ($baseGroups as $data) {
            $models[$data['slug']] = array_merge($data, [
                'id' => Str::uuid(),
                'created_at' => $curDate,
            ]);
        }

        $userModel = $models['user'];
        foreach ($relatedGroups as $data) {
            $models[$data['slug']] = array_merge($data, [
                'id' => Str::uuid(),
                'parent_id' => $userModel['id'],
                'created_at' => $curDate,
            ]);
        }

        foreach ($models as $data) {
            DB::table('perm_groups')->insert($data);
        }
    }
}
