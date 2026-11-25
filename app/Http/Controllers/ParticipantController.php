<?php
namespace App\Http\Controllers;
use App\Models\{Participant,Region,AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class ParticipantController extends Controller {
    public function index(Request $r) {
        $r->validate(['q'=>'nullable|string|max:100','region_id'=>'nullable|exists:regions,id','status'=>['nullable',Rule::in(['aktif','nonaktif'])]]);
        $q=Participant::with(['region','latestService']);
        if ($search=trim($r->input('q',''))) $q->where(fn($q)=>$q->where('name','like','%'.addcslashes($search,'%_\\').'%')->orWhere('nik_hash',Participant::nikHash($search)));
        $participants=$q->when($r->region_id,fn($q,$v)=>$q->where('region_id',$v))->when($r->status,fn($q,$v)=>$q->where('status',$v))->latest()->paginate(12)->withQueryString();
        return view('participants.index',['participants'=>$participants,'regions'=>Region::orderBy('name')->get()]);
    }
    public function create() { return $this->form(new Participant); }
    public function edit(Participant $participant) { AuditLog::record('Buka formulir identitas','Peserta',$participant->id); return $this->form($participant); }
    private function form(Participant $participant) { return view('participants.form',['participant'=>$participant,'regions'=>Region::orderBy('name')->pluck('name','id')]); }
    private function validated(Request $r,?Participant $participant=null): array {
        $data=$r->validate(['nik'=>['required','digits:16',function($attr,$value,$fail)use($participant){ if(Participant::where('nik_hash',Participant::nikHash($value))->when($participant,fn($q)=>$q->where('id','!=',$participant->id))->exists()) $fail('NIK sudah terdaftar. Gunakan peserta yang sudah ada.'); }],'name'=>'required|string|max:150','birth_date'=>'required|date|before_or_equal:today|after:1900-01-01','spouse_name'=>'nullable|string|max:150','address'=>'required|string|max:1000','region_id'=>'required|exists:regions,id','phone'=>['nullable','regex:/^\+?[0-9]{9,15}$/'],'children_count'=>'required|integer|between:0,30']);
        if ($participant && ($first=$participant->firstService) && $data['birth_date']>$first->service_date->toDateString()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['birth_date'=>'Tanggal lahir tidak boleh setelah riwayat pelayanan pertama.']);
        }
        return $data;
    }
    public function store(Request $r) {
        $data=$this->validated($r);
        $p=DB::transaction(function()use($data){$p=new Participant($data); $p->created_by=auth()->id(); $p->save(); AuditLog::record('Tambah','Peserta',$p->id); return $p;});
        return redirect()->route('participants.show',$p)->with('success','Peserta berhasil didaftarkan. Anda dapat mencatat pelayanan pertama.');
    }
    public function show(Participant $participant) {
        AuditLog::record('Lihat identitas dan riwayat','Peserta',$participant->id);
        $participant->load('region','latestService'); $services=$participant->services()->with(['facility','officer'])->orderByDesc('service_date')->orderByDesc('id')->paginate(10);
        return view('participants.show',compact('participant','services'));
    }
    public function update(Request $r,Participant $participant) { $data=$this->validated($r,$participant); DB::transaction(function()use($participant,$data){$participant->update($data); AuditLog::record('Ubah','Peserta',$participant->id);}); return redirect()->route('participants.show',$participant)->with('success','Data peserta diperbarui.'); }
    public function destroy(Participant $participant) {
        DB::transaction(function()use($participant){ $participant=Participant::whereKey($participant->id)->lockForUpdate()->firstOrFail(); abort_if($participant->services()->exists(),422,'Peserta memiliki riwayat pelayanan dan tidak dapat dihapus.'); AuditLog::record('Hapus','Peserta',$participant->id); $participant->delete(); });
        return redirect()->route('participants.index')->with('success','Data peserta dihapus.');
    }
}
