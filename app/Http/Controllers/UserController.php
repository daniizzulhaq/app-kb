<?php
namespace App\Http\Controllers;
use App\Models\{User,AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class UserController extends Controller {
    public function index() { return view('users.index',['users'=>User::orderBy('name')->paginate(15)]); }
    public function create() { return view('users.form',['user'=>new User]); }
    public function edit(User $user) { return view('users.form',compact('user')); }
    private function validated(Request $r,?User $user=null): array {
        $r->merge(['username'=>strtolower(trim((string)$r->input('username'))),'email'=>$r->filled('email')?strtolower(trim($r->email)):null]);
        return $r->validate(['name'=>'required|string|max:150','username'=>['required','string','min:3','max:50','regex:/\A[a-z0-9][a-z0-9._-]{2,49}\z/',Rule::unique('users')->ignore($user?->id)],'email'=>['nullable','email','max:150',Rule::unique('users')->ignore($user?->id)],'password'=>[$user?'nullable':'required','string','min:10','max:128','confirmed'],'role'=>['required',Rule::in(['admin','petugas','pimpinan'])],'is_active'=>'required|boolean']);
    }
    public function store(Request $r) { $data=$this->validated($r); DB::transaction(function()use($data){$u=User::create($data); AuditLog::record('Tambah','Pengguna',$u->id);}); return redirect()->route('users.index')->with('success','Pengguna ditambahkan.'); }
    public function update(Request $r,User $user) {
        $data=$this->validated($r,$user); if(empty($data['password'])) unset($data['password']);
        if($user->id===auth()->id() && ($data['role']!=='admin' || !$data['is_active'])) return back()->withErrors(['role'=>'Akun Anda sendiri harus tetap menjadi admin aktif.']);
        DB::transaction(function()use($user,$data){ User::where('role','admin')->orderBy('id')->lockForUpdate()->get(); $user->refresh();
            if($user->role==='admin' && $user->is_active && ($data['role']!=='admin'||!$data['is_active']) && User::where('role','admin')->where('is_active',true)->count()<=1) abort(422,'Sistem memerlukan setidaknya satu admin aktif.');
            $user->update($data); AuditLog::record('Ubah','Pengguna',$user->id);
        });
        return redirect()->route('users.index')->with('success','Pengguna diperbarui.');
    }
    public function destroy(User $user) {
        if($user->id===auth()->id()) return back()->withErrors(['delete'=>'Akun yang sedang digunakan tidak dapat dihapus.']);
        if(DB::table('services')->where('officer_id',$user->id)->exists()||DB::table('participants')->where('created_by',$user->id)->exists()) return back()->withErrors(['delete'=>'Pengguna memiliki riwayat pencatatan. Nonaktifkan akun melalui tombol edit.']);
        DB::transaction(function()use($user){User::where('role','admin')->orderBy('id')->lockForUpdate()->get(); if($user->role==='admin'&&$user->is_active&&User::where('role','admin')->where('is_active',true)->count()<=1) abort(422,'Admin aktif terakhir tidak dapat dihapus.'); AuditLog::record('Hapus','Pengguna',$user->id); $user->delete();});
        return back()->with('success','Pengguna dihapus.');
    }
}
