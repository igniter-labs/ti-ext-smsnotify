<?php

namespace IgniterLabs\SmsNotify\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Create templates table
 */
class DeleteSmsNotifyLogsTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('igniterlabs_smsnotify_logs');
    }

    public function down()
    {
    }
}
