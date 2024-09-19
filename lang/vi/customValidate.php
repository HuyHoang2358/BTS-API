<?php

return [
    "user" => [
        "name" => [
            "required" => "Tên người dùng không được để trống",
            "string" => "Tên người dùng phải là chuỗi",
            "between" => "Tên phải có độ dài từ 2 đến 100 ký tự",
        ],
        'email' => [
            'required' => 'Địa chỉ email không được để trống',
            'valid' => 'Địa chỉ email không đúng định dạng email',
            'exist' => 'Địa chỉ email không tồn tại',
            'unique' => 'Địa chỉ email đã tồn tại, vui lòng chọn địa chỉ email khác',
        ],
        'password' => [
            'required' => 'Mật khẩu không được để trống',
            'min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'confirmed' => 'Mật khẩu nhập lại không trùng khớp',
        ],
    ],
    "address" => [
        "country" => [
            "name" => [
                "required" => "Tên quốc gia không được để trống",
                "string" => "Tên quốc gia phải là chuỗi",
                "between" => "Tên quốc gia phải có độ dài từ 2 đến 100 ký tự",
                "unique" => "Tên quốc gia đã tồn tại",
            ],
            "code" => [
                "required" => "Mã quốc gia không được để trống",
                "string" => "Mã quốc gia phải là chuỗi",
                "max" => "Mã quốc gia phải có độ dài nhỏ hơn 10 ký tự",
                "unique" => "Mã quốc gia đã tồn tại",
            ],
            "phone_code" => [
                "required" => "Mã điện thoại không được để trống",
                "string" => "Mã điện thoại phải là chuỗi",
                "between" => "Mã điện thoại phải có độ dài từ 2 đến 4 ký tự",
                "unique" => "Mã điện thoại đã tồn tại",
            ],
            "currency" => [
                "required" => "Đơn vị tiền tệ không được để trống",
                "string" => "Đơn vị tiền tệ phải là chuỗi",
            ],
            "language" => [
                "required" => "Ký hiệu ngôn ngữ không được để trống",
                "between" => "Ký hiệu ngôn ngữ phải có độ dài là 2 ký tự",
                "alpha" => "Ký hiệu ngôn ngữ chỉ bao gồm 2 ký tự chữ cái",
            ],
        ],
        "province" => [
            "name" => [
                "required" => "Tên tỉnh, thành phố không được để trống",
                "string" => "Tên tỉnh, thành phố  phải là chuỗi",
                "between" => "Tên tỉnh, thành phố  phải có độ dài từ 2 đến 100 ký tự",
                "unique" => "Tên tỉnh, thành phố đã tồn tại",
                "alpha" => "Tên tỉnh, thành phố chỉ được bao gồm chữ cái",
            ],
            "code" => [
                "required" => "Mã tỉnh, thành phố không được để trống",
                "string" => "Mã tỉnh, thành phố phải là chuỗi",
                "max" => "Mã tỉnh, thành phố phải có độ dài nhỏ hơn 2 ký tự",
                "unique" => "Mã tỉnh, thành phố đã tồn tại",
            ],
            "country_id" => [
                "required" => "ID quốc gia không được để trống",
                "exists" => "ID quốc gia không tồn tại",
                "integer" => "ID quốc gia phải là số",
            ],
        ],
        "district" => [
            "name" => [
                "required" => "Tên quận, huyện không được để trống",
                "string" => "Tên quận, huyện phải là chuỗi",
                "between" => "Tên quận, huyện phải có độ dài từ 2 đến 100 ký tự",
            ],
            "code" => [
                "required" => "Mã quận, huyện không được để trống",
                "string" => "Mã quận, huyện phải là chuỗi",
                "max" => "Mã quận, huyện phải có độ dài nhỏ hơn 3 ký tự",
                "unique" => "Mã quận, huyện đã tồn tại",
            ],
            "province_id" => [
                "required" => "ID tỉnh, thành phố không được để trống",
                "exists" => "ID tỉnh, thành phố không tồn tại",
                "integer" => "ID tỉnh, thành phố phải là số",
            ],
        ],
        "commune" => [
            "name" => [
                "required" => "Tên xã, phường không được để trống",
                "string" => "Tên xã, phường phải là chuỗi",
                "between" => "Tên xã, phường phải có độ dài từ 2 đến 100 ký tự",
            ],
            "code" => [
                "required" => "Mã xã, phường không được để trống",
                "string" => "Mã xã, phường phải là chuỗi",
                "max" => "Mã xã, phường phải có độ dài nhỏ hơn 5 ký tự",
                "unique" => "Mã xã, phường đã tồn tại",
            ],
            "district_id" => [
                "required" => "ID quận, huyện không được để trống",
                "exists" => "ID quận, huyện không tồn tại",
                "integer" => "ID quận, huyện phải là số",
            ],
            "windy_area_id" => [
                "integer" => "ID vùng gió phải là số",
            ],
        ],
    ],
    "windy-area" => [
        "name" => [
            "required" => "Tên vùng gió không được để trống",
            "max" => "Tên vùng gió có độ dài nhỏ hơn 5 ký tự",
            "string" => "Tên vùng gió phải là chuỗi",
            "unique" => "Tên vùng gió đã tồn tại",
            "exists" => "Tên vùng gió không tồn tại",
        ],
        "wo" => [
            "integer" => "Wo phải là số nguyên",
        ],
        "v3s50" => [
            "integer" => "Vận tốc 3s, 50 năm phải là số nguyên",
        ],
        "v10m50" => [
            "integer" => "Vận tốc 10 phút, 50 năm là số nguyên",
        ],
    ],
    "excel" => [
        "file" => [
            "required" => "File không được để trống",
            "mimes" => "File phải có định dạng xlsx hoặc xls",
        ],
        "column" => "Cột",
        "exist" => "không tồn tại trong file excel",
    ],
    'device' => [
        'name' => [
            'required' => 'Tên thiết bị không được để trống',
            'max' => 'Tên thiết bị phải có độ dài nhỏ hơn 500 ký tự',
            'unique' => 'Tên thiết bị đã tồn tại',
        ],
        'description' => [
            'max' => 'Phần miêu tả phải có độ dài nhỏ hơn 1000 ký tự',
        ],
        'model_url' => [
            'max' => 'URL 3D model phải có độ dài nhỏ hơn 2048 ký tự',
        ],
        'length' => [
            'numeric' => 'Chiều dài phải là số thực, cách nhau bởi dấu `.`',
            'between' => 'Chiều dài phải nằm trong khoảng từ 0 đến 1000000',
        ],
        'width' => [
            'numeric' => 'Chiều rộng phải là số thực, cách nhau bởi dấu `.`',
            'between' => 'Chiều rộng phải nằm trong khoảng từ 0 đến 1000000',
        ],
        'depth' => [
            'numeric' => 'Chiều sâu phải là số thực, cách nhau bởi dấu `.`',
            'between' => 'Chiều sâu phải nằm trong khoảng từ 0 đến 1000000',
        ],
        'weight' => [
            'numeric' => 'Trọng lượng phải là số thực, cách nhau bởi dấu `.`',
            'between' => 'Trọng lượng phải nằm trong khoảng từ 0.0 đến 1000000.0',
        ],
        'diameter' => [
            'numeric' => 'Đường kính phải là số thực, cách nhau bởi dấu `.`',
            'between' => 'Đường kính phải nằm trong khoảng từ 0.0 đến 1000000.0',
        ],
        'device_category_id' => [
            'required' => 'ID danh mục thiết bị không được để trống',
            'exists' => 'ID danh mục thiết bị không tồn tại',
        ],
        'vendor_id' => [
            'exists' => 'ID nhà cung cấp không tồn tại',
        ],
        'params' => [
            'string' => 'Danh sách thông số phải là chuỗi',
            'max' => 'Dánh sách thông số phải có độ dài nhỏ hơn 1000 ký tự',
            'regex' => 'Danh sách số không đúng định dạng (ví dụ: `key1:value1, key2:value2`). Không có dấu `,` ở cuối',
        ],
        'vendor' => [
            'name' => [
                "required" => "Tên nhà cung cấp không được để trống",
                "max" => "Tên nhà cung cấp phải có độ dài nhỏ hơn 255 ký tự",
                "unique" => "Tên nhà cung cấp đã tồn tại",
                "exists" => "Tên nhà cung cấp không tồn tại",
            ],
            'description' => [
                "max" => "Phần miêu tả phải có độ dài nhỏ hơn 500 ký tự",
            ],
            'website' => [
                "url" => "URL website không đúng định dạng",
                "max" => "URL website phải có độ dài nhỏ hơn 2048 ký tự",
            ],
            'logo' => [
                "string" => "Dường dẫn logo phải là chuỗi",
                "max" => "Logo phải có độ dài nhỏ hơn 500 ký tự",
            ],
        ],
        'category' => [
            'name' => [
                "required" => "Tên danh mục không được để trống",
                "max" => "Tên danh mục phải có độ dài nhỏ hơn 255 ký tự",
                "unique" => "Tên danh mục đã tồn tại",
                "exists" => "Tên danh mục không tồn tại",
            ],
            'description' => [
                "max" => "Phần miêu tả phải có độ dài nhỏ hơn 500 ký tự",
            ],
        ]
    ],
    'pole' => [
        'name' => [
            'required' => 'Tên cột không được để trống',
            'max' => 'Tên cột phải có độ dài nhỏ hơn 500 ký tự',
            'unique' => 'Tên cột đã tồn tại',
        ],
        'station_code' => [
            'exists' => 'Mã trạm không tồn tại',
        ],
        'height' => [
            'required' => 'Chiều cao cột không được để trống',
            'numeric' => 'Chiều cao cột phải là số',
            'max' => 'Chiều cao cột phải nhỏ hơn 1000',
        ],
        'is_roof' => [
            'required' => 'Cột trên mái hay cột dưới đất không được để trống',
            'boolean' => 'Cột  trên mái hay cột dưới đất phải có kiểu dữ liệu là boolean',
        ],
        'house_height' => [
            'numeric' => 'Chiều cao nhà phải là số',
            'max' => 'Chiều cao nhà phải nhỏ hơn 1000',
        ],
        'pole_category_id' => [
            'required' => 'ID loại cột không được để trống',
            'exists' => 'ID loại cột không tồn tại',
        ],
        'size' => [
            'string' => 'Kích thước phải là chuỗi',
            'max' => 'Kích thước phải có độ dài nhỏ hơn 255 ký tự',
        ],
        'diameter_body_tube' => [
            'numeric' => 'Đường kính ống thân phải là số',
            'string' => 'Kích thước đường kính ống thâ cột là chuỗi',
            'max' => 'Đường kính ống thân phải nhỏ hơn 10,000',
        ],
        'diameter_strut_tube' => [
            'numeric' => 'Đường kính ống thanh chống phải là số',
            'string' => 'Kích thước đường kính ống thanh chống là chuỗi',
            'max' => 'Đường kính ống chân phải nhỏ hơn 10,000',
        ],
        'diameter_top_tube' => [
            'numeric' => 'Đường kính ống thân cột mép trên phải là số',
            'string' => 'Kích thước đường kính ống thân cột mép trên là chuỗi',
            'max' => 'Đường kính ống đỉnh phải nhỏ hơn 10,000',
        ],
        'diameter_bottom_tube' => [
            'numeric' => 'Đường kính ống thân cột mép dưới phải là số',
            'string' => 'Kích thước đường kính ống thân cột mép dưới là chuỗi',
            'max' => 'Đường kính ống đáy phải nhỏ hơn 10,000',
        ],
        'foot_size' => [
            'string' => 'Kích thước chân cột là chuỗi',
            'max' => 'Kích thước chân phải có độ dài nhỏ hơn 50 ký tự',
        ],
        'top_size' => [
            'string' => 'Kích thước đỉnh cột phải là chuỗi',
            'max' => 'Kích thước đỉnh phải có độ dài nhỏ hơn 50 ký tự',
        ],
        'params' => [
            'array' => 'Danh sách thông số phải là mảng',
        ],
        'params.*.key' => [
            'string' => 'Tên thông số phải là chuỗi',
            'max' => 'Tên thông số phải có độ dài nhỏ hơn 255 ký tự',
        ],
        'params.*.value' => [
            'string' => 'Giá trị thông số phải là chuỗi',
            'max' => 'Giá trị thông số phải có độ dài nhỏ hơn 255 ký tự',
        ],
        'structure' => [
            'string' => 'Cấu trúc phải là chuỗi',
            'max' => 'Cấu trúc phải có độ dài nhỏ hơn 255 ký tự',
        ],
        'description' => [
            'string' => 'Miêu tả phải là chuỗi',
            'max' => 'Miêu tả phải có độ dài nhỏ hơn 1000 ký tự',
        ],
        'category' => [
            'name' => [
                "required" => "Tên loại cột không được để trống",
                "max" => "Tên loại cột phải có độ dài nhỏ hơn 255 ký tự",
                "unique" => "Tên loại cột đã tồn tại",
                "exists" => "Tên loại cột không tồn tại",
            ],
            'code' => [
                "required" => "Tên viết tắt của loại cột không được để trống",
                "max" => "Tên viết tắt của loại cột phải có độ dài nhỏ hơn 255 ký tự",
                "unique" => "Tên viết tắt của loại cột đã tồn tại",
                "exists" => "Tên viết tắt của loại cột không tồn tại",
            ],
            'description' => [
                "max" => "Phần miêu tả phải có độ dài nhỏ hơn 500 ký tự",
            ],
        ],
        'devices' =>[
            'required' => 'Danh sách thiết bị không được để trống',
            'array' => 'Danh sách thiết bị phải là mảng',
            'min' => 'Danh sách thiết bị phải có ít nhất 1 thiết bị',
            'id'=> [
                'required' => 'ID thiết bị không được để trống',
                'exists' => 'ID thiết bị không tồn tại',
            ],
            'name' => [
                'required' => 'Tên thiết bị không được để trống',
                'exists' => 'Tên thiết bị không tồn tại',
            ],
            'depth' => [
                'required' => 'Chiều cao thiết bị không được để trống',
                'numeric' => 'Chiều cao thiết bị phải là số',
                'min' => 'Chiều cao thiê bị phải lớn hơn hoặc bằng 0',
            ],
            'width' => [
                'required' => 'Chiều rộng thiết bị không được để trống',
                'numeric' => 'Chiều rộng thiết bị phải là số',
                'min' => 'Chiều rộng thiết bị phải lớn hơn hoặc bằng 0',
            ],
            'height' => [
                'required' => 'Chiều cao thiết bị không được để trống',
                'numeric' => 'Chiều cao thiết bị phải là số',
                'min' => 'Chiều cao thiết bị phải lớn hơn hoặc bằng 0',
            ],
            'weight' => [
                'required' => 'Trọng lượng thiết bị không được để trống',
                'numeric' => 'Trọng lượng thiết bị phải là số',
                'min' => 'Trọng lượng thiết bị phải lớn hơn hoặc bằng 0',
            ],
            'DC' => [
                'required' => 'Khoảng cách từ trọng tâm thiết bị đến trọng tâm cột không được để trống',
                'integer' => 'Khoảng cách từ trọng tâm thiết bị đến trọng tâm cột phải là số nguyên',
                'between' => 'Khoảng cách từ trọng tâm thiết bị đến trọng tâm cột phải nằm trong khoảng từ 0 đến 100',
            ],
        ],
    ],
    'station' => [
        'name' => [
            'required' => 'Tên trạm không được để trống',
            'max' => 'Tên trạm phải có độ dài nhỏ hơn 500 ký tự',
        ],
        'code' => [
            'required' => 'Mã trạm không được để trống',
            'string' => 'Mã trạm phải là chuỗi',
            'max' => 'Mã trạm phải có độ dài nhỏ hơn 100 ký tự',
            'unique' => 'Mã trạm đã tồn tại',
        ],
        'description' => [
            'string' => 'Miêu tả phải là chuỗi',
            'max' => 'Miêu tả phải có độ dài nhỏ hơn 1000 ký tự',
        ],
        'location_latitude' => [
            'required' => 'Vĩ độ không được để trống',
            'numeric' => 'Vĩ độ phải là số',
            'between' => 'Vĩ độ phải nằm trong khoảng từ -90 đến 90',
        ],
        'location_longitude' => [
            'required' => 'Kinh độ không được để trống',
            'numeric' => 'Kinh độ phải là số',
            'between' => 'Kinh độ phải nằm trong khoảng từ -180 đến 180',
        ],
        'location_height' => [
            'required' => 'Độ cao không được để trống',
            'numeric' => 'Độ cao phải là số',
            'max' => 'Độ cao phải nhỏ hơn 1000',
        ],
        'address_detail' => [

            'string' => 'Địa chỉ đường, xá, ... phải là chuỗi',
            'max' => 'Địa chỉ phải có độ dài nhỏ hơn 500 ký tự',
        ],
        'address_country_id' => [
            'required' => 'ID quốc gia không được để trống',
            'exists' => 'ID quốc gia không tồn tại',
            'integer' => 'ID quốc gia phải là số',
        ],
        'address_province_id' => [
            'required' => 'ID tỉnh, thành phố không được để trống',
            'exists' => 'ID tỉnh, thành phố không tồn tại',
            'integer' => 'ID tỉnh, thành phố phải là số',
        ],
        'address_district_id' => [
            'required' => 'ID quận, huyện không được để trống',
            'exists' => 'ID quận, huyện không tồn tại',
            'integer' => 'ID quận, huyện phải là số',
        ],
        'address_commune_id' => [
            'required' => 'ID xã, phường không được để trống',
            'exists' => 'ID xã, phường không tồn tại',
            'integer' => 'ID xã, phường phải là số',
        ]
    ]
];
