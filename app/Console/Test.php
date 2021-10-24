<?php

namespace App\Console;

use App\Models\PermGroup;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Test extends \Illuminate\Console\Command
{
    protected $signature = 'test';

    public function handle()
    {
        $user = User::find('c7dd3c2f-f95d-4e7f-a27e-375883aab4af');
        $group = $user->permGroup;

        $groups = $group->ancestorsAndSelf;
        $ids = $groups->pluck('id')->all();
        array_push($ids, $user->id);

        $perms = Permission::whereIn('owner_id', $ids)
            ->select('permission', 'allow')
            ->distinct()
            ->get()
            ->all();

        dd($perms);
    }
}
