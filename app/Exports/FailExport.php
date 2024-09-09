<?php

namespace App\Exports;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class FailExport implements FromCollection
{
    protected Collection $failures;
    protected UploadedFile $originalFile;
    protected $data;
    protected int $endColumnIndex;


    public function __construct($failures, $originalFile)
    {
        $this->failures = $failures;
        $this->originalFile = $originalFile;

        // Đọc dữ liệu từ file gốc
        $this->data = Excel::toArray((object)null, $originalFile)[0];
        // remove  row have empty value
        // remove row empty value

        $this->data = array_filter($this->data, function ($row) {
            return !empty(array_filter($row, function ($cell) {
                return $cell !== null;
            }));
        });

        /*$this->data = array_filter($this->data, function ($row) {
            return $row[0] !== null && $row[1] !== null;
        });*/
        // set end column index
        $header  = $this->data[0];
        // remove cell empty in header
        $header = array_filter($header, function ($cell) {
            return $cell !== null;
        });
        $this->endColumnIndex = count($header)-1;

    }

    public function collection(): Collection
    {
        $statusColumnIndex = $this->endColumnIndex + 1; // Chỉ số của cột "status"
        $noteColumnIndex = $statusColumnIndex + 1; // Chỉ số của cột "note"

        // Thêm tiêu đề cho cột "status" và "note"
        $this->data[0][$statusColumnIndex] = __('excel.status');
        $this->data[0][$noteColumnIndex] = __('excel.note');

        // Duyệt qua tất cả các hàng (bỏ qua hàng tiêu đề) và đặt giá trị mặc định cho cột "status"
        foreach ($this->data as $rowIndex => &$row) {
            if ($rowIndex === 0) continue;
            $row[$statusColumnIndex] = __('excel.success'); // Mặc định là "success"
        }

        // Thêm cột "status" và "note" dựa trên lỗi
        foreach ($this->failures as $failure) {
            $rowIndex = $failure->row() - 1; // Chỉ số hàng (bắt đầu từ 0)
            $this->data[$rowIndex][$statusColumnIndex] = __('excel.fail'); // Đặt "fail" nếu có lỗi
            // Cộng thêm thông tin lỗi vào ô
            $this->data[$rowIndex][$noteColumnIndex] =
                ($this->data[$rowIndex][$noteColumnIndex] ?? '') // Kiểm tra nếu giá trị hiện tại là null, nếu có gán chuỗi rỗng
                . (empty($this->data[$rowIndex][$noteColumnIndex]) ? '' : ', '. PHP_EOL) // Thêm dấu phân cách nếu đã có dữ liệu
                .implode(', ', $failure->errors()); // Nối các thông báo lỗi vào cột "note"
        }

        return collect($this->data);
    }
}
