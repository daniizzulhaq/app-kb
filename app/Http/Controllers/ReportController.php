<?php
namespace App\Http\Controllers;
use App\Models\{Region,Facility,Service,AuditLog};
use App\Services\Statistics;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\{Spreadsheet,Writer\Xlsx};
use PhpOffice\PhpSpreadsheet\Style\Fill;
class ReportController extends Controller {
    private function data(Request $r): array {
        $f=Statistics::filters($r); $rows=Statistics::report($f); $summary=Statistics::summary($f);
        $regions=Region::orderBy('name')->pluck('name','id'); $facilities=Facility::orderBy('name')->pluck('name','id');
        $scope=implode(' • ',array_filter([$f['month']?Statistics::months()[$f['month']-1]:'Tahunan',$f['year'],$f['region_id']?($regions[$f['region_id']]??''):null,$f['facility_id']?($facilities[$f['facility_id']]??''):null,$f['method']]));
        $scoped=(bool)($f['region_id']||$f['facility_id']||$f['method']);
        return compact('f','rows','summary','regions','facilities','scope','scoped');
    }
    public function index(Request $r) { return view('reports.index',$this->data($r)); }
    public function export(Request $r,string $format) {
        abort_unless(in_array($format,['xlsx','pdf'],true),404); $data=$this->data($r);
        AuditLog::record('Ekspor '.strtoupper($format),'Laporan agregat'); $filename='laporan-kb-'.$data['f']['year'].($data['f']['month']?'-'.str_pad($data['f']['month'],2,'0',STR_PAD_LEFT):'');
        if($format==='pdf') return Pdf::loadView('reports.pdf',$data)->setPaper('a4','landscape')->download($filename.'.pdf');
        $book=new Spreadsheet; $sheet=$book->getActiveSheet(); $sheet->setTitle('Rekap Pelayanan KB');
        $book->getProperties()->setCreator(config('app.institution'))->setTitle(config('app.name').' — Laporan Pelayanan KB');
        $sheet->setCellValue('A1',config('app.name').' — '.config('app.institution').' — LAPORAN PELAYANAN KB'); $sheet->mergeCells('A1:K1');
        // Explicit string values prevent spreadsheet formula injection in facility/region names.
        $sheet->setCellValueExplicit('A2',$data['scope'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); $sheet->mergeCells('A2:K2');
        $headers=['Bulan','Peserta unik','Peserta baru','Pelayanan',...Service::METHODS]; $sheet->fromArray($headers,null,'A4');
        $line=5; foreach($data['rows'] as $row) $sheet->fromArray([$row['label'],$row['unique'],$row['new'],$row['visits'],...$row['methods']],null,'A'.$line++);
        $totals=array_map(fn($i)=>array_sum(array_map(fn($row)=>$row['methods'][$i],$data['rows'])),range(0,6));
        $sheet->fromArray(['Total periode (peserta unik)',$data['summary']['unique'],$data['summary']['new'],$data['summary']['visits'],...$totals],null,'A'.($line+1));
        $sheet->setCellValue('A'.($line+3),'Peserta unik dihitung ulang per periode, bukan penjumlahan peserta setiap bulan. Kolom metode menghitung pelayanan.');
        $sheet->mergeCells('A'.($line+3).':K'.($line+3)); $sheet->getStyle('A4:K4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        if (!$data['scoped']) {
            $sheet->setCellValue('A'.($line+5),'Target peserta unik'); $sheet->setCellValue('B'.($line+5),$data['summary']['target']??'Belum diatur');
            $sheet->setCellValue('A'.($line+6),'Persentase capaian'); $sheet->setCellValue('B'.($line+6),$data['summary']['percentage']!==null?$data['summary']['percentage'].'%':'—');
        }
        $sheet->getStyle('A4:K4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('087F83');
        foreach(range('A','K') as $col) $sheet->getColumnDimension($col)->setAutoSize(true); $sheet->freezePane('B5');
        return response()->streamDownload(function()use($book){(new Xlsx($book))->save('php://output');},$filename.'.xlsx',['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
