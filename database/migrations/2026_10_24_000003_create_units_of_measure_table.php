<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units_of_measure', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // PKT, BTL, AMPL, TUBE, VIAL, PAC, PCS, PAIR, ROLL, BAR, BOX, JAR
            $table->string('name');            // Packet, Bottle, Ampoule, Tube, Vial, Pack, Pieces, Pair, Roll, Bar, Box, Jar
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units_of_measure');
    }
};
