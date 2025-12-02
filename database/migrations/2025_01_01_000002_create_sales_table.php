<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->dateTime('date')->nullable();
            $table->bigInteger('total_price')->default(0);
            $table->enum('status', ['Belum Dibayar','Sudah Dibayar'])->default('Belum Dibayar');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down() { Schema::dropIfExists('sales'); }
};
