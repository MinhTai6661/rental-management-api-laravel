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
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->timestamp('email_verified_at')->nullable();
        //     $table->string('password');
        //     $table->rememberToken();
        //     $table->timestamps();
        // });

        // Schema::create('password_reset_tokens', function (Blueprint $table) {
        //     $table->string('email')->primary();
        //     $table->string('token');
        //     $table->timestamp('created_at')->nullable();
        // });

        // Schema::create('sessions', function (Blueprint $table) {
        //     $table->string('id')->primary();
        //     $table->foreignId('user_id')->nullable()->index();
        //     $table->string('ip_address', 45)->nullable();
        //     $table->text('user_agent')->nullable();
        //     $table->longText('payload');
        //     $table->integer('last_activity')->index();
        // });

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
            $table->enum('role', ['super_admin', 'admin', 'user'])->default('user');
            $table->enum('status', ['active', 'inactive', 'pending_approval'])->default('active');
            $table->string('avatar', 255)->nullable();
            $table->integer('wardId')->nullable();
            $table->text('detailAddress')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('wardId')->references('code')->on('wards')->onDelete('set null');
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['free', 'pro', 'ultra']);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('maxRooms');
            $table->integer('flags')->default(0);
            $table->json('channels')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dormitories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->text('address');
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->uuid('adminId');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('adminId')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('roomNumber', 50);
            $table->decimal('area', 10, 2);
            $table->decimal('rentalPrice', 15, 2);
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->enum('status', ['empty', 'rented', 'repairing'])->default('empty');
            $table->uuid('dormitoryId');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dormitoryId')->references('id')->on('dormitories')->onDelete('cascade');
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('roomId');
            $table->uuid('tenantId');
            $table->dateTime('startDate');
            $table->dateTime('endDate')->nullable();
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('roomId')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('tenantId')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contractId');
            $table->decimal('amount', 15, 2);
            $table->dateTime('dueDate');
            $table->enum('status', ['draft', 'pending', 'paid', 'overdue'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contractId')->references('id')->on('contracts')->onDelete('cascade');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoiceId');
            $table->decimal('amount', 15, 2);
            $table->dateTime('paymentDate');
            $table->string('method', 50);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            $table->foreign('invoiceId')->references('id')->on('invoices')->onDelete('cascade');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('planId');
            $table->uuid('adminId');
            $table->dateTime('startDate');
            $table->dateTime('endDate')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('planId')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('adminId')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('userId');
            $table->string('token', 500);
            $table->dateTime('expiresAt');
            $table->timestamps();

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_oauth', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('userId');
            $table->enum('provider', ['google', 'zalo', 'facebook']);
            $table->string('providerUserId', 255);
            $table->timestamps();

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('users');
    }
};
