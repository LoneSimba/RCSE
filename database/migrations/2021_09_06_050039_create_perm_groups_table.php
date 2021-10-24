<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\{Migrations\Migration, Schema\Blueprint};

class CreatePermGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perm_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('slug', 100)->unique();
            $table->timestamp('created_at')->nullable();
        });

        Schema::table('perm_groups', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('perm_groups')
                ->onUpdate('restrict')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perm_groups');
    }
}
