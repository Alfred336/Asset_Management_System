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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
            $table->string('asset_tag', 100)->unique();
            $table->string('serial_number', 100)->unique()->nullable();
            $table->string('model', 100);
            $table->string('manufacturer', 100)->nullable();
            $table->string('device_type', 100)->comment('e.g., Laptop, Desktop, Server, Mobile, Network Equipment');
            $table->string('operating_system', 100)->nullable();
            $table->string('os_version', 50)->nullable();
            $table->string('processor', 100)->nullable();
            $table->integer('ram_gb')->nullable()->comment('RAM in GB');
            $table->integer('storage_gb')->nullable()->comment('Storage in GB');
            $table->string('storage_type', 50)->nullable()->comment('e.g., SSD, HDD, NVMe');
            $table->string('ip_address', 45)->nullable()->comment('IPv4 or IPv6');
            $table->string('mac_address', 17)->nullable()->comment('MAC address format: XX:XX:XX:XX:XX:XX');
            $table->string('hostname', 100)->nullable();
            $table->string('location', 100)->nullable()->comment('Physical location, e.g., Office, Room, Desk');
            $table->enum('status', ['active', 'offline', 'online', 'formatted', 'dead', 'under_repair', 'retired'])->default('active');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable()->comment('Purchase cost in currency');
            $table->date('warranty_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('staff_id');
            $table->index('asset_tag');
            $table->index('serial_number');
            $table->index('status');
            $table->index('device_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};