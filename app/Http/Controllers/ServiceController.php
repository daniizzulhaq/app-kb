<?php
namespace App\Http\Controllers;
use App\Models\{Participant,Service,Facility,User,AuditLog};
use App\Services\{ServiceLifecycle,Statistics};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class ServiceController extends Controller {
    public function index(Request $r) { $f=Statistics::filters($r); $services=Statistics::query($f)->with(['participant','facility','officer'])->orderByDesc('service_date')->orderByDesc('id')->paginate(12)->withQueryString(); return view('services.index',compact('services','f')); }
    public function create(Request $r) { $participant=$r->participant_id?Participant::findOrFail($r->participant_id):null; return $this->form(new Service,$participant); }
    public function edit(Service $service) { return $this->form($service,$service->participant); }
    private function form(Service $service,?Participant $participant) {
        $facilities=Facility::orderBy('name')->pluck('name','id');
        $officers=User::where(fn($q)=>$q->where('is_active',true)->whereIn('role',['admin','petugas']))->when($service->exists,fn($q)=>$q->orWhere('id',$service->officer_id))->orderBy('name')->pluck('name','id');
        return view('services.form',compact('service','participant','facilities','officers'));
    }
    public function lookup(Request $r) {
        $r->validate(['q'=>'required|string|min:2|max:100']); $search=trim($r->q);
        return Participant::where(fn($q)=>$q->where('name','like','%'.addcslashes($search,'%_\\').'%')->orWhere('nik_hash',Participant::nikHash($search)))->with('region')->limit(12)->get()->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'nik'=>$p->masked_nik,'region'=>$p->region->name]);
    }
    private function validated(Request $r,?Service $service=null): array {
        if (auth()->user()->role==='petugas') $r->merge(['officer_id'=>$service?->officer_id??auth()->id()]);
        $data=$r->validate(['participant_id'=>['required','exists:participants,id',function($a,$v,$fail)use($service){ if($service && (int)$v!==$service->participant_id) $fail('Peserta pada riwayat pelayanan tidak dapat diganti.'); }],'service_date'=>'required|date|before_or_equal:today','method'=>['required',Rule::in(Service::METHODS)],'action'=>['required',Rule::in(Service::ACTIONS)],'facility_id'=>'required|exists:facilities,id','officer_id'=>['required',Rule::exists('users','id')->where(fn($q)=>$q->where(fn($q)=>$q->whereIn('role',['admin','petugas'])->where('is_active',true))->when($service,fn($q)=>$q->orWhere('id',$service->officer_id)))],'notes'=>'nullable|string|max:2000','next_visit_date'=>'nullable|date|after:service_date']);
        $p=Participant::findOrFail($data['participant_id']);
        if ($data['service_date']<$p->birth_date->toDateString()) throw \Illuminate\Validation\ValidationException::withMessages(['service_date'=>'Tanggal pelayanan harus setelah tanggal lahir peserta.']);
        return $data;
    }
    public function store(Request $r) {
        $data=$this->validated($r);
        DB::transaction(function()use($data){ $p=Participant::whereKey($data['participant_id'])->lockForUpdate()->firstOrFail(); $s=new Service($data); $s->participant_type='lama'; $s->save(); ServiceLifecycle::sync($p); AuditLog::record('Tambah','Pelayanan',$s->id); });
        return redirect()->route('participants.show',$data['participant_id'])->with('success','Pelayanan tersimpan. Statistik dan rekapitulasi telah diperbarui.');
    }
    public function update(Request $r,Service $service) {
        $data=$this->validated($r,$service);
        DB::transaction(function()use($data,$service){ $p=Participant::whereKey($service->participant_id)->lockForUpdate()->firstOrFail(); $service->refresh(); if($service->next_visit_date?->toDateString()!==($data['next_visit_date']??null)){ $service->appointment_status='terjadwal'; $service->appointment_note=null; } $service->fill($data)->save(); ServiceLifecycle::sync($p); AuditLog::record('Ubah','Pelayanan',$service->id); });
        return redirect()->route('participants.show',$service->participant_id)->with('success','Pelayanan diperbarui.');
    }
    public function destroy(Service $service) {
        DB::transaction(function()use($service){ $p=Participant::whereKey($service->participant_id)->lockForUpdate()->firstOrFail(); AuditLog::record('Hapus','Pelayanan',$service->id); $service->delete(); ServiceLifecycle::sync($p); });
        return back()->with('success','Pelayanan dihapus dan rekapitulasi diperbarui.');
    }
}
