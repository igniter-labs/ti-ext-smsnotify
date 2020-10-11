<?php

namespace IgniterLabs\SmsNotify\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Schema;

/**
 * Create templates table
 */
class CreateSmsNotifyTables extends Migration
{
    public function up()
    {
        Schema::create('igniterlabs_smsnotify_templates', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code')->unique()->index();
            $table->string('name')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_custom')->nullable();
            $table->timestamps();
        });

        Schema::create('igniterlabs_smsnotify_channels', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code')->unique()->index();
            $table->string('class_name')->nullable()->index();
            $table->text('config_data')->nullable();
            $table->boolean('is_enabled')->default(0)->nullable();
            $table->boolean('is_default')->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('igniterlabs_smsnotify_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('channel')->nullable();
            $table->string('template')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->nullable();
            $table->integer('short_status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('igniterlabs_smsnotify_templates');
        Schema::dropIfExists('igniterlabs_smsnotify_channels');
        Schema::dropIfExists('igniterlabs_smsnotify_logs');
    }
}
