<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{User,Region,Facility,Participant,Service,Target};
use App\Services\ServiceLifecycle;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        DB::transaction(function () {
            $this->call(AccountSeeder::class);
            $this->call(MasterDataSeeder::class);
            if (app()->environment('production')) return;
            $region = Region::where('name','Luwuk')->firstOrFail();

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
                'facility_id'=>Facility::where('name','Ruang ASOKA')->firstOrFail()->id,
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
