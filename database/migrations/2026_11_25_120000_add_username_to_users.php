<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username',50)->nullable()->unique()->after('name');
            $table->string('email')->nullable()->change();
        });
        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            $base=$user->email===$user->role.'@sikb.local'
                ? $user->role : Str::slug(Str::before($user->email??'', '@'), '.');
            if (strlen($base)<3) $base='user-'.$user->id;
            $base=substr($base,0,40); $username=$base; $suffix=1;
            while (DB::table('users')->where('username',$username)->exists()) $username=$base.'.'.$suffix++;
            DB::table('users')->where('id',$user->id)->update(['username'=>$username]);
        }
        Schema::table('users', fn(Blueprint $table)=>$table->string('username',50)->nullable(false)->change());
    }
    public function down(): void {
        if (DB::table('users')->whereNull('email')->exists()) {
            throw new RuntimeException('Isi email resmi untuk semua pengguna sebelum mengembalikan skema login email.');
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']); $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
        });
    }
};
