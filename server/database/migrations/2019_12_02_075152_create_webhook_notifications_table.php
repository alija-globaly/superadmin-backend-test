<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_name');
            $table->json('payload');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
        Schema::create('webhook_notification_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('webhook_notification_id');
            $table->string('webhook_listener_url');
            $table->json('response');
            $table->boolean('status');
            $table->timestamps();

            $table->foreign('webhook_notification_id')
                ->references('id')
                ->on('webhook_notifications')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhook_notification_history');
        Schema::dropIfExists('webhook_notifications');
    }
}
