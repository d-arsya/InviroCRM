<?php

use App\Models\Message;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->char('order_id', 14)->nullable()->unique();
            $table->string('name');
            $table->string('phone');
            $table->date('date');
            $table->date('send');
            $table->integer('total_price');
            $table->integer('total_count');
            $table->json('products');
            $table->enum('status', ['waiting', 'sended', 'failed'])->default('waiting');
            $table->foreignIdFor(Message::class)->nullable()->constrained()->nullOnDelete();
            $table->json('message_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
