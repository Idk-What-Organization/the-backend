<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('google_id');
            $table->string('photo_profile')->nullable()->after('bio');
            $table->string('photo_cover')->nullable()->after('photo_profile');
            $table->string('background')->nullable()->after('photo_cover'); // Bisa untuk warna/gambar
            $table->string('photo_parallax')->nullable()->after('background');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'photo_profile',
                'photo_cover',
                'background',
                'photo_parallax'
            ]);
        });
    }
};
