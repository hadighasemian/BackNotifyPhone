<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $t) {
                $t->string('role')->default('admin')->after('password');
            });
        }

        Schema::create('devices', function (Blueprint $t) {
            $t->id();
            $t->string('device_uuid')->unique();
            $t->string('token', 64)->nullable()->index(); // sha256 of current device token
            $t->string('brand')->nullable();
            $t->string('model')->nullable();
            $t->string('manufacturer')->nullable();
            $t->string('os_version')->nullable();
            $t->unsignedInteger('sdk_int')->nullable();
            $t->string('app_version')->nullable();
            $t->string('fcm_token', 512)->nullable();
            $t->string('label')->nullable();
            $t->boolean('notification_access')->default(false);
            $t->boolean('battery_optimization_ignored')->default(false);
            $t->timestamp('last_seen_at')->nullable()->index();
            $t->timestamps();
        });

        Schema::create('device_states', function (Blueprint $t) {
            $t->id();
            $t->foreignId('device_id')->constrained()->cascadeOnDelete();
            $t->boolean('has_notification_permission')->default(false);
            $t->integer('battery_level')->nullable();
            $t->boolean('is_charging')->default(false);
            $t->string('network_type')->nullable();
            $t->decimal('latitude', 10, 7)->nullable();
            $t->decimal('longitude', 10, 7)->nullable();
            $t->float('location_accuracy')->nullable();
            $t->timestamp('reported_at')->index();
        });

        Schema::create('notifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('device_id')->constrained()->cascadeOnDelete();
            $t->string('device_notif_key');
            $t->string('package_name')->index();
            $t->string('app_label')->nullable();
            $t->text('title')->nullable();
            $t->longText('text')->nullable();
            $t->text('subtext')->nullable();
            $t->timestamp('posted_at')->nullable()->index();
            $t->timestamp('received_at')->useCurrent()->index();
            $t->enum('status', ['received', 'processed'])->default('received')->index();
            $t->json('extras')->nullable();
            $t->unique(['device_id', 'device_notif_key']);
        });

        Schema::create('app_errors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $t->enum('level', ['error', 'warn', 'fatal'])->default('error')->index();
            $t->text('message');
            $t->longText('stacktrace')->nullable();
            $t->string('app_version')->nullable();
            $t->string('os_version')->nullable();
            $t->json('context')->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('app_versions', function (Blueprint $t) {
            $t->id();
            $t->string('version_name');
            $t->unsignedInteger('version_code')->unique();
            $t->string('file_path');
            $t->unsignedBigInteger('file_size');
            $t->string('sha256', 64);
            $t->text('release_notes')->nullable();
            $t->boolean('is_active')->default(false)->index();
            $t->unsignedInteger('downloads')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
        Schema::dropIfExists('app_errors');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('device_states');
        Schema::dropIfExists('devices');
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
        }
    }
};
