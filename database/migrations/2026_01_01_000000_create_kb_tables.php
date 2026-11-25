<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email')->unique(); $t->string('password');
            $t->enum('role', ['admin','petugas','pimpinan'])->default('petugas'); $t->boolean('is_active')->default(true);
            $t->rememberToken(); $t->timestamps();
        });
        Schema::create('regions', function (Blueprint $t) { $t->id(); $t->string('name')->unique(); $t->string('code',30)->unique(); $t->timestamps(); });
        Schema::create('facilities', function (Blueprint $t) {
            $t->id(); $t->foreignId('region_id')->constrained()->restrictOnDelete(); $t->string('name')->unique();
            $t->string('type',50); $t->string('address')->nullable(); $t->timestamps();
        });
        Schema::create('participants', function (Blueprint $t) {
            $t->id(); $t->text('nik'); $t->char('nik_hash',64)->unique(); $t->string('name')->index(); $t->date('birth_date');
            $t->text('spouse_name')->nullable(); $t->text('address'); $t->foreignId('region_id')->constrained()->restrictOnDelete();
            $t->text('phone')->nullable(); $t->unsignedTinyInteger('children_count')->default(0);
            $t->enum('status',['aktif','nonaktif'])->default('nonaktif')->index(); $t->foreignId('created_by')->constrained('users')->restrictOnDelete(); $t->timestamps();
        });
        Schema::create('services', function (Blueprint $t) {
            $t->id(); $t->foreignId('participant_id')->constrained()->restrictOnDelete(); $t->date('service_date')->index();
            $t->enum('participant_type',['baru','lama']); $t->enum('method',['IUD','Implan','Suntik','Pil','Kondom','MOW','MOP'])->index();
            $t->string('action',50); $t->foreignId('facility_id')->constrained()->restrictOnDelete();
            $t->foreignId('officer_id')->constrained('users')->restrictOnDelete(); $t->text('notes')->nullable();
            $t->date('next_visit_date')->nullable()->index(); $t->enum('appointment_status',['terjadwal','selesai','dibatalkan'])->default('terjadwal');
            $t->text('appointment_note')->nullable(); $t->timestamps();
            $t->index(['participant_id','service_date','id']);
        });
        Schema::create('targets', function (Blueprint $t) {
            $t->id(); $t->unsignedSmallInteger('year'); $t->unsignedTinyInteger('month')->default(0);
            $t->unsignedInteger('amount'); $t->timestamps(); $t->unique(['year','month']);
        });
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $t->string('action');
            $t->string('subject'); $t->unsignedBigInteger('subject_id')->nullable(); $t->string('ip_address',45)->nullable(); $t->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { foreach (['audit_logs','targets','services','participants','facilities','regions','users'] as $table) Schema::dropIfExists($table); }
};
