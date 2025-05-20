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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->string('frequency'); // 'monthly', 'weekly', 'yearly', 'once'
            $table->integer('day_of_month')->nullable();
            $table->date('due_date');
            $table->date('next_due_date')->nullable();
            $table->boolean('auto_pay')->default(false);
            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending'); // 'paid', 'pending', 'overdue'
            $table->boolean('is_recurring')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
}; 