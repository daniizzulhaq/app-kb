<?php
namespace App\Services;
use App\Models\{Participant,Service,Target,Region,Facility};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class Statistics {
    public static function filters(Request $r): array {
        $data=$r->validate(['year'=>'nullable|integer|between:2000,2100','month'=>'nullable|integer|between:1,12','region_id'=>'nullable|exists:regions,id','facility_id'=>'nullable|exists:facilities,id','method'=>['nullable',Rule::in(Service::METHODS)]]);
        return ['year'=>(int)($data['year']??now()->year),'month'=>isset($data['month'])?(int)$data['month']:null,'region_id'=>$data['region_id']??null,'facility_id'=>$data['facility_id']??null,'method'=>$data['method']??null];
    }
    public static function query(array $f) {
        return Service::query()->whereYear('service_date',$f['year'])
            ->when($f['month']??null,fn($q,$v)=>$q->whereMonth('service_date',$v))
            ->when($f['region_id']??null,fn($q,$v)=>$q->whereHas('participant',fn($p)=>$p->where('region_id',$v)))
            ->when($f['facility_id']??null,fn($q,$v)=>$q->where('facility_id',$v))
            ->when($f['method']??null,fn($q,$v)=>$q->where('method',$v));
    }
    public static function summary(array $f): array {
        $q=static::query($f);
        $unique=(clone $q)->distinct()->count('participant_id');
        $new=(clone $q)->where('participant_type','baru')->distinct()->count('participant_id');
        $target=Target::where('year',$f['year'])->where('month',$f['month']??0)->value('amount');
        return ['unique'=>$unique,'new'=>$new,'visits'=>(clone $q)->count(),'target'=>$target,'percentage'=>$target?round($unique/$target*100,1):null];
    }
    public static function months(): array { return ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; }
    public static function report(array $f): array {
        $months=$f['month']?[$f['month']]:range(1,12); $rows=[];
        foreach ($months as $month) {
            $scope=[...$f,'month'=>$month]; $summary=static::summary($scope); $q=static::query($scope);
            $counts=(clone $q)->selectRaw('method, COUNT(*) as total')->groupBy('method')->pluck('total','method');
            $rows[]=['month'=>$month,'label'=>static::months()[$month-1],...$summary,'methods'=>array_map(fn($m)=>(int)($counts[$m]??0),Service::METHODS)];
        }
        return $rows;
    }
}
