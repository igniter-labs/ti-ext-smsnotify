<?php

declare(strict_types=1);

namespace IgniterLabs\SmsNotify\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Create templates table
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('igniterlabs_smsnotify_logs');
    }

    public function down(): void {}
};
