<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
class AccountSeeder extends Seeder {
    public function run(): void {
        foreach(['admin'=>'Administrator RSUD Luwuk','petugas'=>'Petugas Pelayanan KB','pimpinan'=>'Pimpinan RSUD Luwuk'] as $role=>$name) {
            if (User::where('username',$role)->exists()) continue;
            $password=config('accounts.passwords.'.$role);
            if (!is_string($password) || strlen($password)<12 || strlen($password)>128) {
                throw new \RuntimeException('Isi INITIAL_'.strtoupper($role).'_PASSWORD di .env dengan kata sandi 12–128 karakter sebelum membuat akun awal.');
            }
            User::create(['username'=>$role,'name'=>$name,'email'=>null,'password'=>$password,'role'=>$role,'is_active'=>true]);
        }
    }
}
