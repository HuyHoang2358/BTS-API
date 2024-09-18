<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:calculation {file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Nhận đường dẫn file từ tham số truyền vào
        $filePath = $this->argument('file_path');

        // Giả lập xử lý lệnh (ví dụ: chờ 3 phút)
        $this->info("Bắt đầu xử lý file: $filePath");

        sleep(10); // Giả lập lệnh mất 3 phút để chạy

        // Hiển thị thông báo hoàn tất
        $this->info("Xử lý hoàn tất cho file: $filePath");
    }
}
