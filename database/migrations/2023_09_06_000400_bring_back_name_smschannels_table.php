<?php

namespace IgniterLabs\SmsNotify\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BringBackNameSmschannelsTable extends Migration
{
    public function up()
    {
        Schema::table('igniterlabs_smsnotify_channels', function(Blueprint $table) {
            $table->text('description')->nullable()->after('class_name');
            $table->string('name')->nullable()->after('class_name');
        });
    }

    public function down()
    {
    }
}
