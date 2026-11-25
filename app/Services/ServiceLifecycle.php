<?php
namespace App\Services;
use App\Models\{Participant,Service};
class ServiceLifecycle {
    // The participant row is locked by the caller, serializing concurrent visits.
    public static function sync(Participant $participant): void {
        $services=$participant->services()->orderBy('service_date')->orderBy('id')->get();
        foreach ($services as $i=>$service) {
            $type=$i===0?'baru':'lama';
            if ($service->participant_type!==$type) { $service->participant_type=$type; $service->save(); }
        }
        $latest=$services->last(fn($service)=>$service->action!=='Konseling');
        $participant->status=$latest && $latest->action!=='Pencabutan'?'aktif':'nonaktif';
        $participant->save();
    }
}
