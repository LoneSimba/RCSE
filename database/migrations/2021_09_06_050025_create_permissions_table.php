<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\{Migrations\Migration, Schema\Blueprint};

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('owner_id');
            $table->string('owner_type', 100);
            $table->string('permission', 500);
            $table->boolean('allow')->default(true);
            $table->timestamps();

            $table->unique(['owner_id', 'owner_type', 'permission']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
