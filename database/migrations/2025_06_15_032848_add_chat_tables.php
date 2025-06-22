<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        //
        Schema::create('chats', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('title');
            $table->timestamps(); // why? 
        });

        Schema::create('chat_users', function (Blueprint $table) {
            /*
            id column is required due to: https://laravel.com/docs/12.x/eloquent#composite-primary-keys
            "Eloquent requires each model to have at least one uniquely identifying "ID" that can serve as its primary key. "Composite" primary keys are not supported by Eloquent models."
            *it would not be required if we used it only as many-to-many relationship resolve without model, but...
            */
            $table->id()->primary();;
            $table->foreignId('chat_id');
            $table->foreignId('user_id');
            // $table->primary(['user_id', 'chat_id']);
            $table->unique(['user_id', 'chat_id']);

            $table->foreign(['user_id'], 'chat_users_to_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign(['chat_id'], 'chat_users_to_chat')
                ->references('id')
                ->on('chats')
                ->cascadeOnDelete();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('chat_id');
            $table->foreignId('user_id');
            $table->string('message');
            $table->timestamps();
            // $table->index(['user_id', 'chat_id']);

            $table->foreign(['user_id', 'chat_id'], 'chat_messages_to_chat_users')
                ->references(['user_id', 'chat_id'])
                ->on('chat_users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        //
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_users');
        Schema::dropIfExists('chats');
    }
};
