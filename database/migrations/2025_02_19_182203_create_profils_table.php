<?php

use App\Models\Administrator;
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
        Schema::create('profils', function (Blueprint $table) {
            $table->id();
            $table->string('lastname');
            $table->string('firstname')->nullable();
            $table->string('picture')->nullable();

            $table->foreignIdFor(Administrator::class, 'user_id')->nullable();

            $statuses = getProfilStatuses();
            if (count($statuses) > 0) {
                $table->enum('status', $statuses)->default($statuses[0]);
            } else {
                $table->string('status')->nullable();
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profils');
    }
};
