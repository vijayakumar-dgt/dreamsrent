<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

class BookingQueryConfig
{
    public const USERNAME_SELECT = 'users.name as username';
    public const FULLNAME_SELECT = "CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name";
    public const DISPLAY_DATE_FORMAT = 'd-m-Y H:i';
    public const DB_DATE_FORMAT = 'Y-m-d H:i:s';
    public const VEHICLE_NAME_SELECT = 'vehicle_info.name as vehicle_name';
    public const VEHICLE_IMAGE_PATH = 'vehicles/images/small/';
    public const STORAGE_PATH = 'storage/';
    public const DEFAULT_COMPANY_NAME = 'Default Company Name';
    public const CUSTOMER_IMAGE_SELECT = 'user_details.profile_image as customer_image';
    public const CUSTOMER_FULLNAME_SELECT = "CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name";
    public const PICKUP_LOCATION_SELECT = 'locations as pickup_location';
    public const DROP_LOCATION_SELECT = 'locations as drop_location';
}
