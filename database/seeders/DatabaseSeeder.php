<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{User,Region,Facility,Participant,Service,Target};
use App\Services\ServiceLifecycle;

class DatabaseSeeder extends Seeder {
    public const ROOMS = [
        'Ruangan perawatan Asoka' => 'Perawatan',
        'Ruang PONEK' => 'PONEK',
        'Ruang poli kandungan' => 'Poliklinik',
        'Instalasi bedah' => 'Bedah',
    ];

    public function run(): void {
        DB::transaction(function () {
            $this->call(AccountSeeder::class);
            if (app()->environment('production')) return;
            $region = Region::firstOrCreate(['code'=>'32.04.01'], ['name'=>'Sukamaju']);

            foreach (self::ROOMS as $name => $type) {
                Facility::firstOrCreate(['name'=>$name], [
                    'region_id'=>$region->id, 'type'=>$type,
                ]);
            }

            foreach ([0, now()->month] as $month) {
                Target::firstOrCreate(['year'=>now()->year, 'month'=>$month], ['amount'=>1]);
            }

            // Do not add demo participants to an existing operational database.
            if (Participant::exists()) return;

            $officer = User::where('role','petugas')->where('is_active',true)->firstOrFail();
            $participant = new Participant([
                'nik'=>'9999000000000001',
                'name'=>'Siti Rahmawati',
                'birth_date'=>'1995-05-12',
                'spouse_name'=>'Bapak Contoh 1',
                'address'=>'Jl. Contoh Data Sintetis No. 1',
                'region_id'=>$region->id,
                'phone'=>'080000000001',
                'children_count'=>1,
            ]);
            $participant->created_by=$officer->id;
            $participant->save();

            $service = new Service([
                'participant_id'=>$participant->id,
                'service_date'=>today()->toDateString(),
                'method'=>'Suntik',
                'action'=>'Pemasangan',
                'facility_id'=>Facility::where('name','Ruangan perawatan Asoka')->firstOrFail()->id,
                'officer_id'=>$officer->id,
                'notes'=>'Data contoh sintetis untuk demonstrasi aplikasi.',
                'next_visit_date'=>today()->addDays(28)->toDateString(),
            ]);
            $service->participant_type='baru';
            $service->save();
            ServiceLifecycle::sync($participant);
        });
    }
}
