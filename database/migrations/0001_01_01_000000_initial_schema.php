<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // provinces table
        Schema::create('provinces', function (Blueprint $table) {
            $table->integer('code')->primary();
            $table->string('name', 255);
            $table->string('division_type', 100)->nullable();
            $table->string('codename', 100)->nullable();
            $table->integer('phone_code')->nullable();
            $table->timestamps();
        });

        // wards table
        Schema::create('wards', function (Blueprint $table) {
            $table->integer('code')->primary();
            $table->string('name', 255);
            $table->string('division_type', 100)->nullable();
            $table->string('codename', 100)->nullable();
            $table->integer('province_code');
            $table->timestamps();

            $table->foreign('province_code')->references('code')->on('provinces')->onDelete('cascade');
        });

        // users table
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email', 255)->unique()->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('password', 255)->nullable();
            $table->string('name', 255);
            $table->string('phone', 20)->nullable();
            $table->enum('status', ['active', 'inactive', 'pending_approval'])->default('active');
            $table->string('avatar', 255)->nullable();
            $table->integer('ward_code')->nullable();
            $table->text('detail_address')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ward_code')->references('code')->on('wards')->onDelete('set null');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->integer('level')->default(0);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            $table->primary(['role_id', 'permission_id']);

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('restrict');
        });

        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['free', 'pro', 'ultra']);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('max_rooms');
            $table->integer('flags')->default(0);
            $table->json('channels')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dormitories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('address');
            $table->text('description')->nullable();
            $table->uuid('landlord_id');
            $table->integer('ward_code')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('landlord_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ward_code')->references('code')->on('wards')->onDelete('set null');
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('size', 10, 2)->nullable();
            $table->decimal('rental_price', 15, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'rented', 'repairing'])->default('available');
            $table->unsignedBigInteger('dormitory_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'dormitory_id']);
            $table->foreign('dormitory_id')->references('id')->on('dormitories')->onDelete('set null');
        });

        Schema::create('room_media', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->integer('order')->default(0);
            $table->string('type')->nullable()->index();
            $table->unsignedBigInteger('room_id');

            $table->timestamps();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('room_id');
            $table->uuid('tenant_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->decimal('amount', 15, 2);
            $table->dateTime('due_date');
            $table->enum('status', ['draft', 'pending', 'paid', 'overdue'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->decimal('amount', 15, 2);
            $table->dateTime('payment_date');
            $table->string('method', 50);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('plan_id');
            $table->uuid('landlord_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('landlord_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('user_id');
            $table->string('token', 500);
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_oauth', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->enum('provider', ['google', 'zalo', 'facebook']);
            $table->string('provider_id', 255);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_oauth');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('dormitories');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('user_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('room_media');

    }
};
