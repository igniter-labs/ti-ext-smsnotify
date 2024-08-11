<?php

namespace IgniterLabs\SmsNotify\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationIdSmschannelsTable extends Migration
{
    public function up()
    {
        Schema::table('igniterlabs_smsnotify_channels', function(Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->after('id');
        });
    }

    public function down() {}
}
