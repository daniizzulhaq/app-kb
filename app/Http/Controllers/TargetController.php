<?php
namespace App\Http\Controllers;
use App\Models\{Target,AuditLog};
use App\Services\Statistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class TargetController extends Controller {
    public function index(Request $r) { $r->validate(['year'=>'nullable|integer|between:2000,2100']); $year=(int)($r->year??now()->year); return view('targets.index',['year'=>$year,'targets'=>Target::where('year',$year)->pluck('amount','month')]); }
    public function store(Request $r) {
        $data=$r->validate(['year'=>'required|integer|between:2000,2100','targets'=>'required|array|size:13','targets.*'=>'nullable|integer|between:0,10000000']);
        foreach(range(0,12) as $month) if(!array_key_exists($month,$data['targets'])) abort(422,'Isian periode target tidak lengkap.');
        DB::transaction(function()use($data){foreach(range(0,12) as $month){$amount=$data['targets'][$month]; if($amount===null) Target::where('year',$data['year'])->where('month',$month)->delete(); else Target::updateOrCreate(['year'=>$data['year'],'month'=>$month],['amount'=>$amount]);} AuditLog::record('Simpan target tahun '.$data['year'],'Target');});
        return redirect()->route('targets.index',['year'=>$data['year']])->with('success','Target capaian disimpan.');
    }
}
