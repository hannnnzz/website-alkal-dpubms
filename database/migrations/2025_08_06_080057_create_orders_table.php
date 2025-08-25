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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('order_id')->unique();
            $table->string('provider_name');
            $table->string('customer_name');
            $table->string('customer_contact');

            $table->date('test_date');
            $table->string('file_upload_path')->nullable();
            $table->integer('amount')->default(0);
            $table->enum('status', ['UNPAID','PENDING', 'PAID', 'CANCELLED', 'EXPIRED'])->default('UNPAID');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('tanggal_masuk')->useCurrent();
            $table->string('hari')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('no_surat')->nullable();
            $table->text('alamat_pengirim')->nullable();
            $table->string('perihal')->nullable();
            $table->enum('disposisi', ['Alat Berat', 'Laboratorium'])->default('Alat Berat');
            $table->text('deskripsi_paket_pekerjaan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
