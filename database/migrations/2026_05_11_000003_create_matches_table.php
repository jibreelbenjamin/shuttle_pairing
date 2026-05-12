<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->integer('babak'); // 1 = babak 1, 2 = babak 2, dst
            $table->integer('urutan'); // urutan pertandingan di babak tersebut
            $table->foreignId('participant1_id')->nullable()->constrained('participants')->onDelete('set null');
            $table->foreignId('participant2_id')->nullable()->constrained('participants')->onDelete('set null');
            $table->foreignId('pemenang_id')->nullable()->constrained('participants')->onDelete('set null');
            $table->boolean('is_by')->default(false); // apakah ini slot BY
            $table->boolean('is_selesai')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
