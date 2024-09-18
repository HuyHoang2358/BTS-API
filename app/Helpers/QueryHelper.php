<?php
namespace App\Helpers;

use App\Enums\ApiMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QueryHelper
{
    // TODO: Apply sort
    public static function applySort($query, Request $request)
    {
        // Sort by fields if request sort=field1,-field2

        $sort = $request->query('sort', '');
        if ($sort === '') return $query;
        //if (!$request->query('sort') !== null) return $query;

        //return $query->orderBy('name', 'ASC');
        collect(explode(',', $request->query('sort', '')))
            ->filter()
            ->each(function ($field) use ($query) {
                $direction = Str::startsWith($field, '-') ? 'DESC' : 'ASC';
                $query->orderBy(ltrim($field, '-'), $direction);
            });
        return $query;
    }

    // TODO: Apply filter
    /*
     * Các loại filter:
     *   filter[field]=value: Lọc theo điều kiện bằng (=).
     *   filter[field.starts_with]=value: Lọc theo chuỗi bắt đầu với giá trị.
     *   filter[field.ends_with]=value: Lọc theo chuỗi kết thúc với giá trị.
     *   filter[field.contains]=value: Lọc theo chuỗi chứa giá trị.
     *   filter[field.in]=value1,value2,...: Lọc trong danh sách giá trị.
     *   filter[field.starts_between]=date1,date2: Lọc giữa hai ngày.
     *   filter[field.greater_than]=value: Lọc lớn hơn giá trị.
     *   filter[field.less_than]=value: Lọc nhỏ hơn giá trị.
     *   filter[field.greater_or_equal]=value: Lọc lớn hơn hoặc bằng.
     *   filter[field.less_or_equal]=value: Lọc nhỏ hơn hoặc bằng.
    */
    public static function applyFilter($query, Request $request)
    {
        // Lấy giá trị các filter từ request
        if ($filters = $request->query('filter', [])) {
            foreach ($filters as $field => $value) {
                if ($value === '' || $value === null) continue;
                if (Str::contains($field, '.')) {
                    // Lấy thông tin cột và phép toán filter
                    [$column, $operation] = explode('.', $field);

                    switch ($operation) {
                        case 'starts_with':
                            $query->where($column, 'like', "$value%");
                            break;
                        case 'ends_with':
                            $query->where($column, 'like', "%$value");
                            break;
                        case 'contains':
                            $query->where($column, 'like', "%$value%");
                            break;
                        case 'in':
                            Log::error($value);
                            $values = explode(',', $value);
                            $query->whereIn($column, $values);
                            break;
                        case 'starts_between':
                            $dates = explode(',', $value);
                            if (count($dates) === 2) {
                                $query->whereBetween($column, [$dates[0], $dates[1]]);
                            }
                            break;
                        case 'greater_than':
                            $query->where($column, '>', $value);
                            break;
                        case 'less_than':
                            $query->where($column, '<', $value);
                            break;
                        case 'greater_or_equal':
                            $query->where($column, '>=', $value);
                            break;
                        case 'less_or_equal':
                            $query->where($column, '<=', $value);
                            break;
                        default:
                            break;
                    }
                } else {
                    // Simple equals filter (filter[field]=value)
                    $query->where($field, $value);
                }
            }
        }
        return $query;
    }

    // TODO: Apply paginate
    public static function applyPaginate($query, Request $request, $hidden = [])
    {
        $perPage = $request->query('perPage','');
        // validate perPage
        if ($perPage !== '') {
            $perPage = (int)$perPage;
        }
        return $perPage === '' ? $query->get()->makeHidden($hidden) : $query->paginate($perPage);
    }


    // TODO: Apply Search
    public static function applySearch($query, Request $request, $columns)
    {
        // Lấy giá trị tìm kiếm từ query 'search'
        if ($searchTerm = $request->query('searchValue')) {
            $searchTerm = self::removeAccents(strtolower($searchTerm)); // Chuyển thành lowercase và loại bỏ dấu

            $query->where(function ($query) use ($columns, $searchTerm) {
                foreach ($columns as $column) {
                    // Tìm kiếm trong từng cột
                    $query->orWhereRaw("LOWER(CONVERT($column USING utf8)) LIKE ?", ["%$searchTerm%"]);
                }
            });
        }

        return $query;
    }

    // TODO: Apply Query
    public static function applyQuery($query, Request $request, $searchColumns = [] , $with = [], $hidden = [])
    {
        $query = $query->with($with);
        $query = self::applyFilter($query, $request);
        $query = self::applySort($query, $request);
        $query = self::applySearch($query, $request, $searchColumns);
        return self::applyPaginate($query, $request, $hidden);
    }

    private static function removeAccents($str): string
    {
        $unwantedArray = [
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
            'đ' => 'd', 'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A',
            'Ă' => 'A', 'Ắ' => 'A', 'Ằ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A',
            'Â' => 'A', 'Ấ' => 'A', 'Ầ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A',
            'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E',
            'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O',
            'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
            'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U',
            'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U',
            'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'Đ' => 'D',
        ];
        return strtr($str, $unwantedArray);
    }
}
