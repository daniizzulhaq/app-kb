<?php
namespace Database\Seeders;

use App\Models\{Facility,Region};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder {
    public const REGIONS = [
        'Balantak',
        'Balantak Selatan',
        'Balantak Utara',
        'Batui',
        'Batui Selatan',
        'Bualemo',
        'Bunta',
        'Kintom',
        'Lamala',
        'Lobu',
        'Luwuk',
        'Luwuk Selatan',
        'Luwuk Timur',
        'Luwuk Utara',
        'Mantoh',
        'Masama',
        'Moilong',
        'Nambo',
        'Nuhon',
        'Pagimana',
        'Simpang Raya',
        'Toili',
        'Toili Barat',
        'Toili Jaya',
    ];

    public const ROOMS = [
        'Ruang PONEK' => 'PONEK',
        'Ruang ASOKA' => 'Perawatan',
        'Ruang Poli kandungan' => 'Poliklinik',
        'Instalasi Bedah Sentral' => 'Bedah',
        'Ruang VIP Tulip' => 'Perawatan',
        'Ruang VIP Sakura' => 'Perawatan',
        'Ruang VIP Bougenville' => 'Perawatan',
    ];

    private const LEGACY_ROOMS = [
        'Ruang ASOKA' => 'Ruangan perawatan Asoka',
        'Ruang Poli kandungan' => 'Ruang poli kandungan',
        'Instalasi Bedah Sentral' => 'Instalasi bedah',
    ];

    public function run(): void {
        DB::transaction(function () {
            foreach (self::REGIONS as $name) {
                // Internal identifiers, not official administrative region codes.
                Region::firstOrCreate(['name'=>$name], ['code'=>'BANGGAI-'.Str::upper(Str::slug($name))]);
            }

            $region = Region::where('name','Luwuk')->firstOrFail();
            foreach (self::ROOMS as $name => $type) {
                $facility = Facility::where('name',$name)->first();
                if (!$facility && isset(self::LEGACY_ROOMS[$name])) {
                    $facility = Facility::where('name',self::LEGACY_ROOMS[$name])->first();
                }
                $facility ??= new Facility;
                // Keep existing IDs so service history continues to use the same rooms.
                $facility->fill(['name'=>$name, 'type'=>$type, 'region_id'=>$region->id])->save();
            }
        });
    }
}
