<?php

namespace Modules\Page\Support;

final class PageConstants
{
    public const PAGES = 'pages/';
    public const PLACEHOLDER_BANNER = 'frontend/assets/img/banner/placeholder-banner.jpg';
    public const LIMIT_VIEWALL_ORDER_REGEX = '/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/';
    public const TYPE_LIMIT_VIEWALL_REGEX = '/type=([a-zA-Z]+)\s+limit=(\d+)\s+viewall=(yes|no)/';
    public const LIMIT_VIEWALL_REGEX = '/limit=(\d+)\s+viewall=(yes|no)/';
    public const STORAGE_PATH = 'storage/';
    public const DEFAULT_AVATAR_01 = 'backend/assets/img/profiles/avatar-01.jpg';
    public const DEFAULT_AVATAR_02 = 'backend/assets/img/profiles/avatar-02.jpg';
    public const DEFAULT_AVATAR_03 = 'backend/assets/img/profiles/avatar-03.jpg';
    public const PLACEHOLDER_APP_CAR = 'frontend/assets/img/placeholder-app-car.jpg';
    public const DEFAULT_IMAGE = 'images/default.png';
    public const CAR_TYPE_SELECT = 'carType:id,name';
    public const BRAND_SELECT = 'brand:id,brand_name';
    public const CATEGORY_SELECT = 'category:id,name';
    public const MAIN_LOCATION_SELECT = 'mainLocation:id,name';
    public const COLOR_SELECT = 'color:id,name,value';
    public const FUEL_TYPE_SELECT = 'fuel_type:id,fuel_type';
    public const TRANSMISSION_SELECT = 'transmission:id,name';
    public const VEHICLE_IMAGE_SMALL = 'vehicles/images/small/';
    public const VEHICLE_IMAGE = 'vehicles/images/';
    public const DEFAULT_PROFILE = '/backend/assets/img/default-profile.png';
    public const APP_PUBLIC_PATH = 'app/public/';
    public const STORAGE_PATHS = '/storage/';
    public const ICON_SELECTION = '/frontend/assets/img/icons/bx-selection.svg';
    public const FIND_VEHICLES_SERVICE = 'Find the best vehicles and services easily.';
}
