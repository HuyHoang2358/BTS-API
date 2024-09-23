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
        ],
        'status' => [
            'init'=> 'Khởi tạo',
            'processing'=> 'Đang xử lý',
            'done'=> 'Hoàn thành',
            'error'=> 'Lỗi',
        ],
    ]
];
