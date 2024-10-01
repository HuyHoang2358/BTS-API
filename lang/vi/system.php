<?php

return [
    'process' => [
        'step'=> [
            'init'=> [
                'name' => 'Khởi tạo',
                'logs'=> [
                    'init'=> 'Khởi tạo dữ liệu thành công',
                ]
            ],
            'image_processing'=>[
                'name'=>'Xử lý hình ảnh 2D',
                'metadata'=>'Dữ liệu hình ảnh',
                'save_image_info'=>'Lưu thông tin hình ảnh vào cơ sở dữ liệu',
            ],
            'generate_3d_model'=>[
                'name'=>'Tái tạo mô hình 3D',
                'logs'=>[
                    'total_images'=>'Tổng số hình ảnh',
                ]
            ],
            'extract_information' => [
                'name' => 'Trích xuất thông tin thiết bị trên cột',
                'logs' => [
                    'extracted' => 'Trích xuất thông tin thành công',
                ]
            ],
        ],
        'status' => [
            'init'=> 'Khởi tạo',
            'processing'=> 'Đang xử lý',
            'done'=> 'Hoàn thành',
            'error'=> 'Lỗi',
        ],
    ],
];

