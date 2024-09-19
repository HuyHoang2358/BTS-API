<?php
namespace App\Enums;

enum ApiMessage: string
{
    case VALIDATE_FAIL = '422|validate_fail';
    case FILE_FORMAT_FAIL = '404|file_format_fail';
    case LOGOUT_SUCCESS = '200|logout_success';
    case USER_PROFILE = '2000|user.profile';
    case AUTH_UNAUTHORIZED = '1401|unauthorized';

    case ADDRESS_IMPORT_SUCCESS = '030000|excel.import-success';
    case ADDRESS_IMPORT_FAIL = '030001|excel.import-fail';

    case ADDRESS_COUNTRY_LIST = '030100|address.country.list';
    case ADDRESS_COUNTRY_NOT_FOUND = '030101|address.country.not-found';
    case ADDRESS_COUNTRY_STORE_SUCCESS = '030102|address.country.add-success';
    case ADDRESS_COUNTRY_UPDATE_SUCCESS = '030103|address.country.update-success';
    case ADDRESS_COUNTRY_DESTROY_SUCCESS = '030104|address.country.delete-success';

    case ADDRESS_PROVINCE_LIST = '030200|address.province.list';
    case ADDRESS_PROVINCE_NOT_FOUND = '030201|address.province.not-found';
    case ADDRESS_PROVINCE_STORE_SUCCESS = '030202|address.province.add-success';
    case ADDRESS_PROVINCE_UPDATE_SUCCESS = '030203|address.province.update-success';
    case ADDRESS_PROVINCE_DESTROY_SUCCESS = '030204|address.province.delete-success';

    case ADDRESS_DISTRICT_LIST = '030300|address.district.list';
    case ADDRESS_DISTRICT_NOT_FOUND = '030301|address.district.not-found';
    case ADDRESS_DISTRICT_STORE_SUCCESS = '030302|address.district.add-success';
    case ADDRESS_DISTRICT_UPDATE_SUCCESS = '030303|address.district.update-success';
    case ADDRESS_DISTRICT_DESTROY_SUCCESS = '030304|address.district.delete-success';

    case ADDRESS_COMMUNE_LIST = '030300|address.commune.list';
    case ADDRESS_COMMUNE_NOT_FOUND = '030301|address.commune.not-found';
    case ADDRESS_COMMUNE_STORE_SUCCESS = '030302|address.commune.add-success';
    case ADDRESS_COMMUNE_UPDATE_SUCCESS = '030303|address.commune.update-success';
    case ADDRESS_COMMUNE_DESTROY_SUCCESS = '030304|address.commune.delete-success';

    case WINDY_AREA_LIST = '040100|windy-area.list';
    case WINDY_AREA_NOT_FOUND = '040101|windy-area.not-found';
    case WINDY_AREA_STORE_SUCCESS = '040102|windy-area.add-success';
    case WINDY_AREA_UPDATE_SUCCESS = '040103|windy-area.update-success';
    case WINDY_AREA_DESTROY_SUCCESS = '040104|windy-area.delete-success';

    case DEVICE_IMPORT_SUCCESS = '050000|excel.import-success';
    case DEVICE_IMPORT_FAIL = '050001|excel.import-fail';
    case DEVICE_LIST = '050002|device.list';
    case DEVICE_NOT_FOUND = '050003|device.not-found';
    case DEVICE_STORE_SUCCESS = '050004|device.add-success';
    case DEVICE_UPDATE_SUCCESS = '050005|device.update-success';
    case DEVICE_DESTROY_SUCCESS = '050006|device.delete-success';

    case DEVICE_VENDOR_LIST = '050100|device.vendor.list';
    case DEVICE_VENDOR_NOT_FOUND = '050101|device.vendor.not-found';
    case DEVICE_VENDOR_STORE_SUCCESS = '050102|device.vendor.add-success';
    case DEVICE_VENDOR_UPDATE_SUCCESS = '050103|device.vendor.update-success';
    case DEVICE_VENDOR_DESTROY_SUCCESS = '050104|device.vendor.delete-success';

    case DEVICE_CATEGORY_LIST = '050200|device.category.list';
    case DEVICE_CATEGORY_NOT_FOUND = '050201|device.category.not-found';
    case DEVICE_CATEGORY_STORE_SUCCESS = '050202|device.category.add-success';
    case DEVICE_CATEGORY_UPDATE_SUCCESS = '050203|device.category.update-success';
    case DEVICE_CATEGORY_DESTROY_SUCCESS = '050204|device.category.delete-success';
    case DEVICE_CATEGORY_HAS_DEVICE = '050205|device.category.has-device';


    case POLE_IMPORT_SUCCESS = '060000|excel.import-success';
    case POLE_IMPORT_FAIL = '060001|excel.import-fail';
    case POLE_LIST = '060002|pole.list';
    case POLE_NOT_FOUND = '060003|pole.not-found';
    case POLE_STORE_SUCCESS = '060004|pole.add-success';
    case POLE_UPDATE_SUCCESS = '060005|pole.update-success';
    case POLE_DESTROY_SUCCESS = '060006|pole.delete-success';
    case POLE_DEVICE_STORE_SUCCESS = '060007|pole.device.add-success';
    case POLE_DEVICE_REMOVE_SUCCESS = '060008|pole.device.remove-success';

    case POLE_CATEGORY_LIST = '060100|pole.category.list';
    case POLE_CATEGORY_NOT_FOUND = '060101|pole.category.not-found';
    case POLE_CATEGORY_STORE_SUCCESS = '060102|pole.category.add-success';
    case POLE_CATEGORY_UPDATE_SUCCESS = '060103|pole.category.update-success';
    case POLE_CATEGORY_DESTROY_SUCCESS = '060104|pole.category.delete-success';
    case POLE_CATEGORY_HAS_DEVICE = '060105|pole.category.has-device';

    case STATION_IMPORT_SUCCESS = '070000|excel.import-success';
    case STATION_IMPORT_FAIL = '070001|excel.import-fail';
    case STATION_LIST = '070002|station.list';
    case STATION_NOT_FOUND = '070003|station.not-found';
    case STATION_STORE_SUCCESS = '070004|station.add-success';
    case STATION_UPDATE_SUCCESS = '070005|station.update-success';
    case STATION_DESTROY_SUCCESS = '070006|station.delete-success';
    case STATION_POLE_STORE_SUCCESS = '070007|station.pole.add-success';
    case STATION_POLE_REMOVE_SUCCESS = '070007|station.pole.remove-success';

    case UPLOAD_TYPE_NOT_SUPPORT = '100000|file.type-not-support';
    case UPLOAD_SUCCESS = '100001|file.upload-success';
    case POLE_STRESS_SUCCESS = '110000|pole.stress-success';


    case SUCCESS = '1000|authenticated';
    case ERROR = '1001|An error occurred';
    case USER_NOT_FOUND = '1002|User not found';
    case USER_CREATED = '1003|User created successfully';

    // Rule for code format:
    // 1_XXX : Auth
    // 2_XXX : User
    // 3_XXX : Address


    public function code(): string
    {
    return explode('|', $this->value)[0];
    }

    public function message(): string
    {
    return __('apiMessage.' .explode('|', $this->value)[1]);
    }
}
