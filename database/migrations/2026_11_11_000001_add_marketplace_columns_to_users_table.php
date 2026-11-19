<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('buyer')->index()->after('password');
            $table->string('nim', 30)->nullable()->after('role');
            $table->string('phone', 30)->nullable()->after('nim');
            $table->text('address')->nullable()->after('phone');
            $table->boolean('is_blocked')->default(false)->after('address');
            $table->string('blocked_reason')->nullable()->after('is_blocked');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'nim', 'phone', 'address', 'is_blocked', 'blocked_reason']);
        });
    }
};
