<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropNameSmschannelsTable extends Migration
{
    public function up(): void
    {
        Schema::table('igniterlabs_smsnotify_channels', function(Blueprint $table): void {
            $table->dropColumn('name');
        });
    }

    public function down() {}
}
