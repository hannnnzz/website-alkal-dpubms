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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('alat_id')->nullable()->after('type'); // FK ke alat_sewa_types bila type = 'Sewa'
            $table->date('rental_start')->nullable()->after('quantity');
            $table->date('rental_end')->nullable()->after('rental_start');
            $table->bigInteger('price')->default(0)->after('rental_end'); // total price untuk item ini
            // optional: add foreign key if you want
            // $table->foreign('alat_id')->references('id')->on('alat_sewa_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['alat_id','rental_start','rental_end','price']);
        });
    }
};
