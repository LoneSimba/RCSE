<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\{Migrations\Migration, Schema\Blueprint};

class AddPermGroupIdColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('perm_group_id')->after('remember_token')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('perm_group_id');
        });
    }
}
