<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudViaticos', function (Blueprint $table) {
            $table->unsignedBigInteger('revisor_id')->nullable();
            $table->foreign('revisor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitudViaticos', function (Blueprint $table) {
            $table->unsignedBigInteger('revisor_id')->nullable();
            $table->foreign('revisor_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
