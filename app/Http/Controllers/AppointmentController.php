<?php
namespace App\Http\Controllers;
use App\Models\{Service,AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class AppointmentController extends Controller {
    public function index(Request $r) {
        $r->validate(['status'=>['nullable',Rule::in(['terjadwal','selesai','dibatalkan','terlambat'])],'date'=>'nullable|date']); $status=$r->input('status','terjadwal');
        $appointments=Service::with(['participant.region','facility','officer'])->whereNotNull('next_visit_date')
            ->when($status==='terlambat',fn($q)=>$q->where('appointment_status','terjadwal')->whereDate('next_visit_date','<',today()),fn($q)=>$q->where('appointment_status',$status))
            ->when($r->date,fn($q,$v)=>$q->whereDate('next_visit_date',$v))->orderBy('next_visit_date')->paginate(12)->withQueryString();
        return view('appointments',compact('appointments','status'));
    }
    public function update(Request $r,Service $service) {
        abort_unless($service->next_visit_date,404);
        $data=$r->validate(['appointment_status'=>['required',Rule::in(['terjadwal','selesai','dibatalkan'])],'appointment_note'=>'nullable|string|max:1000']);
        if ($data['appointment_status']==='selesai' && $service->next_visit_date->isFuture()) return back()->withErrors(['appointment_status'=>'Jadwal mendatang belum dapat diselesaikan.']);
        DB::transaction(function()use($service,$data){ $service->appointment_status=$data['appointment_status']; $service->appointment_note=$data['appointment_note']??null; $service->save(); AuditLog::record('Ubah jadwal: '.$data['appointment_status'],'Pelayanan',$service->id); });
        return back()->with('success','Status jadwal diperbarui. Catat tindakan kunjungan pada menu pelayanan.');
    }
}
