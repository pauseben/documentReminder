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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('taj')->unique(); // Nemadható meg id-ként és integerként sem mert 0-val nem kezdődhet ID az adatbázisban
            $table->string('name');
            $table->string('email');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('reminder_2week')->nullable();
            $table->timestamp('reminder_1week')->nullable();
            $table->timestamp('reminder_3day')->nullable();
            $table->timestamp('reminder_1day')->nullable();
            //$table->text('documents_expires_at')->nullable();//Tovább fejleszéshez, ha nem csak 1db bizonyos dokumentom lesz
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
        Schema::dropIfExists('people');
    }
};
