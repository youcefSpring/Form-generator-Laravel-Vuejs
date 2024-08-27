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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('label')
                  ->comment("Field label (e.g., 'First Name', 'Email')");

            $table->string('name')
                  ->comment("Field name (e.g., 'first_name', 'email')");

            $table->string('type')
                   ->comment("Field type (e.g., 'text', 'email', 'select')");

            $table->json('options')
                   ->nullable()
                   ->comment(" JSON field for options if the type is select, radio, etc");

            $table->boolean('is_required')
                   ->default(true);

            $table->enum('category', ['general', 'identity', 'bank-related'])
                  ->default('general');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
