<?php
namespace App\Http\Controllers;
use App\Models\{Region,Facility,AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class MasterController extends Controller {
    private function model(string $kind): string { abort_unless(in_array($kind,['regions','facilities'],true),404); return $kind==='regions'?Region::class:Facility::class; }
    public function index(string $kind) { $model=$this->model($kind); $items=($kind==='regions'?$model::withCount('participants'):$model::with('region')->withCount('services'))->orderBy('name')->paginate(15); return view('master.index',compact('kind','items')); }
    public function create(string $kind) { $model=$this->model($kind); return $this->form($kind,new $model); }
    public function edit(string $kind,int $id) { $model=$this->model($kind); return $this->form($kind,$model::findOrFail($id)); }
    private function form(string $kind,$item) { return view('master.form',['kind'=>$kind,'item'=>$item,'regions'=>Region::orderBy('name')->pluck('name','id')]); }
    private function validated(Request $r,string $kind,?int $id=null): array {
        $this->model($kind);
        return $r->validate($kind==='regions'?['name'=>['required','string','max:150',Rule::unique('regions')->ignore($id)],'code'=>['required','string','max:30',Rule::unique('regions')->ignore($id)]]:['name'=>['required','string','max:150',Rule::unique('facilities')->ignore($id)],'region_id'=>'required|exists:regions,id','type'=>['required',Rule::in(Facility::TYPES)],'address'=>'nullable|string|max:255']);
    }
    public function store(Request $r,string $kind) { $data=$this->validated($r,$kind); $model=$this->model($kind); DB::transaction(function()use($model,$data,$kind){$item=$model::create($data); AuditLog::record('Tambah',$kind,$item->id);}); return redirect()->route('master.index',$kind)->with('success','Data berhasil ditambahkan.'); }
    public function update(Request $r,string $kind,int $id) { $model=$this->model($kind); $item=$model::findOrFail($id); $data=$this->validated($r,$kind,$id); DB::transaction(function()use($item,$data,$kind){$item->update($data); AuditLog::record('Ubah',$kind,$item->id);}); return redirect()->route('master.index',$kind)->with('success','Data diperbarui.'); }
    public function destroy(string $kind,int $id) {
        $model=$this->model($kind); $item=$model::findOrFail($id);
        if (($kind==='regions' && ($item->participants()->exists()||$item->facilities()->exists())) || ($kind==='facilities' && $item->services()->exists())) return back()->withErrors(['delete'=>'Data sedang digunakan dan tidak dapat dihapus.']);
        DB::transaction(function()use($item,$kind){AuditLog::record('Hapus',$kind,$item->id); $item->delete();}); return back()->with('success','Data dihapus.');
    }
}
