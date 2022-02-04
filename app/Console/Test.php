<?php

namespace App\Console;

use App\Contracts\Repositories\PermissionRepository;
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


        $perms = app(PermissionRepository::class)->findForPermissionableWithAncestors($user)->groupBy('owner_id');
        $userPerms = $perms->get($user->id)->pluck('allow', 'permission');

        $perms->each(function ($group, $owner) use (&$userPerms, $user) {
            if ($owner !== $user->id) {
                $keys = $userPerms->keys();
                $data = $group->filter(function ($perm) use ($keys) {
                    return !in_array($perm->permission, $keys);
                });
                dump($data);


            }
        });

        dd($userPerms);
    }
}
