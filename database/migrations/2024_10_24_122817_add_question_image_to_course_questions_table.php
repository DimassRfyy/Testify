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
        Schema::table('course_questions', function (Blueprint $table) {
            $table->string('questionImage')->nullable()->after('question'); // Menambahkan kolom questionImage
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_questions', function (Blueprint $table) {
            $table->dropColumn('questionImage'); // Menghapus kolom questionImage saat rollback
        });
    }
};
