<?php
namespace App\Http\Controllers;
use App\Models\{Participant,Service,Target,Region,Facility};
use App\Services\Statistics;
use Illuminate\Http\Request;
class DashboardController extends Controller {
    public function __invoke(Request $r) {
        $f=Statistics::filters($r); $f['region_id']=$f['facility_id']=$f['method']=null;
        $summary=Statistics::summary($f);
        $registered=Participant::count(); $active=Participant::where('status','aktif')->count();
        $yearScope=[...$f,'month'=>null];
        $monthly=Statistics::report($yearScope);
        $methods=Statistics::query($f)->selectRaw('method, COUNT(*) as total')->groupBy('method')->pluck('total','method');
        $regions=Region::orderBy('name')->get()->map(fn($item)=>['name'=>$item->name,'total'=>Statistics::query([...$f,'region_id'=>$item->id])->distinct()->count('participant_id')]);
        $facilities=Facility::orderBy('name')->get()->map(fn($item)=>['name'=>$item->name,'total'=>Statistics::query([...$f,'facility_id'=>$item->id])->distinct()->count('participant_id')]);
        $recent=auth()->user()->canEdit()?Statistics::query($f)->with(['participant.region','facility','officer'])->orderByDesc('service_date')->orderByDesc('id')->limit(5)->get():collect();
        $appointments=auth()->user()->canEdit()?Service::with(['participant','facility'])->whereNotNull('next_visit_date')->where('appointment_status','terjadwal')->orderBy('next_visit_date')->limit(4)->get():collect();
        $chart=['labels'=>array_map(fn($m)=>mb_substr($m,0,3),Statistics::months()),'monthly'=>array_column($monthly,'unique'),'visits'=>array_column($monthly,'visits'),'targets'=>array_column($monthly,'target'),'methods'=>array_map(fn($m)=>(int)($methods[$m]??0),Service::METHODS),'methodLabels'=>Service::METHODS,'regions'=>$regions,'facilities'=>$facilities];
        return view('dashboard',compact('f','summary','registered','active','monthly','recent','appointments','chart'));
    }
}
