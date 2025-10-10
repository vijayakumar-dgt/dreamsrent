<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $user = currentUser();
        $userType = $user->user_type ?? '';

        // Admin type 1 bypasses permissions except reservation check
        if ($userType === 1) {
            if ($this->isReservationDisabled($routeName)) {
                return redirect()->route('dashboard')
                    ->with('permission-error', 'Currently this menu is disabled!');
            }
            return $next($request);
        }

        // Admin type 2 requires permission check
        if ($userType === 2) {
            $permissions = getUserPermissions();

            if ($this->isReservationRoute($routeName)) {
                return $this->handleReservationRoute($routeName, $permissions, $next);
            }

            return $this->handleGeneralRoute($routeName, $permissions, $next);
        }

        // Default: allow
        return $next($request);
    }

    /**
     * Map routes to module/action.
     */
    private function getRouteModules(): array
    {
        return [
            'dashboard'            => ['module' => 'dashboard', 'action' => 'view'],
            'doorType.index'       => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'brand.index'          => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'cartypes'             => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'damage-types'         => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'tags'                 => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'carModel.index'       => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'carSeat.index'        => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'carColor.index'       => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'carTrasmission.index' => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'fuelType.index'       => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'steeringType.index'   => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'category.index'       => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'seasons'              => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'cylinders'            => ['module' => 'vehicle_attributes', 'action' => 'view'],
            'safetyFeature.index'  => ['module' => 'vehicle_attributes', 'action' => 'view'],

            'admin.testimoials' => ['module' => 'testimonials', 'action' => 'view'],
            'admin.faq'         => ['module' => 'faq', 'action' => 'view'],
            'admin.howItWorks'  => ['module' => 'how_it_works', 'action' => 'view'],
            'admin.copyright'   => ['module' => 'copyright', 'action' => 'view'],

            'driver.index'    => ['module' => 'drivers', 'action' => 'view'],
            'locations'       => ['module' => 'locations', 'action' => 'view'],
            'admin.customers' => ['module' => 'customers', 'action' => 'view'],

            'country.index' => ['module' => 'cms_locations', 'action' => 'view'],
            'state.index'   => ['module' => 'cms_locations', 'action' => 'view'],
            'city.index'    => ['module' => 'cms_locations', 'action' => 'view'],

            'admin.rental-settings'          => ['module' => 'rental_settings', 'action' => 'view'],
            'insurance.index'                => ['module' => 'rental_settings', 'action' => 'view'],
            'admin.signature-settings'       => ['module' => 'app_settings', 'action' => 'view'],
            'admin.invoiceSettings-settings' => ['module' => 'app_settings', 'action' => 'view'],
            'admin.tax-rates'                => ['module' => 'finance_settings', 'action' => 'view'],
            'admin.tax-group-store'          => ['module' => 'finance_settings', 'action' => 'view'],
            'admin.currencies'               => ['module' => 'finance_settings', 'action' => 'view'],
            'admin.bankindex-settings'       => ['module' => 'finance_settings', 'action' => 'view'],
            'admin.paymentIndex-settings'    => ['module' => 'finance_settings', 'action' => 'view'],

            'admin.email-settings'        => ['module' => 'system_settings', 'action' => 'view'],
            'admin.smsGateway-settings'   => ['module' => 'system_settings', 'action' => 'view'],
            'email_templates.index'       => ['module' => 'system_settings', 'action' => 'view'],
            'admin.gdpr-cookies-settings' => ['module' => 'system_settings', 'action' => 'view'],
            'admin.company-settings'      => ['module' => 'website_settings', 'action' => 'view'],
            'admin.logo-settings'         => ['module' => 'website_settings', 'action' => 'view'],
            'admin.localization'          => ['module' => 'website_settings', 'action' => 'view'],
            'admin.prefixes-settings'     => ['module' => 'website_settings', 'action' => 'view'],
            'admin.seosetup-settings'     => ['module' => 'website_settings', 'action' => 'view'],
            'admin.maintenance-settings'  => ['module' => 'website_settings', 'action' => 'view'],
            'admin.ai-configuration'      => ['module' => 'website_settings', 'action' => 'view'],
            'admin.otp-settings'          => ['module' => 'website_settings', 'action' => 'view'],
            'admin.languages'             => ['module' => 'website_settings', 'action' => 'view'],
            'admin.addonIndex-settings'   => ['module' => 'website_settings', 'action' => 'view'],

            'admin.sitemap'                => ['module' => 'other_settings', 'action' => 'view'],
            'admin.storage-settings'       => ['module' => 'other_settings', 'action' => 'view'],
            'admin.system-backup-settings' => ['module' => 'other_settings', 'action' => 'view'],
            'admin.database-settings'      => ['module' => 'other_settings', 'action' => 'view'],

            'admin.profile-settings'       => ['module' => 'account_settings', 'action' => 'view'],
            'admin.security-settings'      => ['module' => 'account_settings', 'action' => 'view'],
            'admin.notifications-settings' => ['module' => 'account_settings', 'action' => 'view'],
            'extra_services'               => ['module' => 'extra_service', 'action' => 'view'],
            'inspection.index'             => ['module' => 'inspections', 'action' => 'view'],
            'maintenance.index'            => ['module' => 'maintenance', 'action' => 'view'],
            'vehicle.list'                 => ['module' => 'vehicles', 'action' => 'view'],
            'edit.car'                     => ['module' => 'vehicles', 'action' => 'edit'],
            'vehicle.vehicleadd'           => ['module' => 'vehicles', 'action' => 'create'],
            'admin.users'                  => ['module' => 'users', 'action' => 'view'],
            'admin.roles-permisions'       => ['module' => 'roles_permissions', 'action' => 'view'],

            'admin.pageIndex'            => ['module' => 'page', 'action' => 'view'],
            'admin.addPage'              => ['module' => 'page', 'action' => 'create'],
            'admin.editPage'             => ['module' => 'page', 'action' => 'edit'],
            'admin.indexSection'         => ['module' => 'section', 'action' => 'view'],
            'admin.menu'                 => ['module' => 'menu_management', 'action' => 'view'],
            'admin.menuManagement'       => ['module' => 'menu_management', 'action' => 'edit'],
            'admin.newsletters'          => ['module' => 'newsletters', 'action' => 'view'],
            'communication.announcement' => ['module' => 'announcements', 'action' => 'view'],

            'quotations.index'             => ['module' => 'quotations', 'action' => 'view'],
            'quotations.create'            => ['module' => 'quotations', 'action' => 'create'],
            'quotations.edit'              => ['module' => 'quotations', 'action' => 'edit'],
            'calendar.index'               => ['module' => 'calendar', 'action' => 'view'],
            'reservation.index'            => ['module' => 'reservations', 'action' => 'view'],
            'reservation.create'           => ['module' => 'reservations', 'action' => 'create'],
            'reservation.edit'             => ['module' => 'reservations', 'action' => 'edit'],
            'reservation.details'          => ['module' => 'reservations', 'action' => 'view'],
            'admin.invoice'                => ['module' => 'invoices', 'action' => 'view'],
            'admin.addInvoice'             => ['module' => 'invoices', 'action' => 'create'],
            'invoices.edit'                => ['module' => 'invoices', 'action' => 'edit'],
            'communication.ticket'         => ['module' => 'tickets', 'action' => 'view'],
            'communication.ticket-details' => ['module' => 'tickets', 'action' => 'view'],
            'payment.payment'              => ['module' => 'payments', 'action' => 'view'],
            'enquiry.index'                => ['module' => 'enquiries', 'action' => 'view'],
            'admin.blogs'                  => ['module' => 'blogs', 'action' => 'view'],
            'admin.blog-add'               => ['module' => 'blogs', 'action' => 'create'],
            'blog.edit'                    => ['module' => 'blogs', 'action' => 'edit'],
            'blog.details'                 => ['module' => 'blogs', 'action' => 'view'],
            'admin.blog-comments'          => ['module' => 'blogs', 'action' => 'view'],
            'admin.blog-tags'              => ['module' => 'blogs', 'action' => 'view'],
            'admin.blog-category'          => ['module' => 'blogs', 'action' => 'view'],
            'admin.reviews'                => ['module' => 'reviews', 'action' => 'view'],
        ];
    }

    /**
     * Check if a route is a reservation route.
     */
    private function isReservationRoute(?string $routeName): bool
    {
        $reservationRoutes = [
            'reservation.index',
            'reservation.create',
            'reservation.edit',
            'reservation.details'
        ];

        return in_array($routeName, $reservationRoutes, true);
    }

    /**
     * Check if reservation menu is disabled (type 1 users).
     */
    private function isReservationDisabled(?string $routeName): bool
    {
        return $this->isReservationRoute($routeName) && !isAccessMenu('reservation');
    }

    /**
     * Handle reservation routes for type 2 users.
     */
    private function handleReservationRoute(?string $routeName, Collection $permissions, Closure $next): Response
    {
        if (!isAccessMenu('reservation')) {
            return $this->redirectWithMessage(__('admin.common.menu_disabled'));
        }

        $routeModules = $this->getRouteModules();
        $moduleDetails = $routeModules[$routeName] ?? null;

        if ($moduleDetails && hasPermission($permissions, $moduleDetails['module'], $moduleDetails['action'])) {
            return $next(request());
        }

        $redirectRoute = hasPermission($permissions, 'dashboard', 'view') ? 'dashboard' : 'admin.profile-settings';
        return $this->redirectWithMessage(__('admin.common.permission_access_denied'), $redirectRoute);
    }

    /**
     * Handle non-reservation routes for type 2 users.
     */
    private function handleGeneralRoute(?string $routeName, Collection $permissions, Closure $next): Response
    {
        $routeModules = $this->getRouteModules();
        $moduleDetails = $routeModules[$routeName] ?? null;

        if ($moduleDetails && hasPermission($permissions, $moduleDetails['module'], $moduleDetails['action'])) {
            return $next(request());
        }

        $redirectRoute = hasPermission($permissions, 'dashboard', 'view') ? 'dashboard' : 'admin.profile-settings';
        return $this->redirectWithMessage(__('admin.common.permission_access_denied'), $redirectRoute);
    }

    /**
     * Helper: redirect with flash message
     */
    private function redirectWithMessage(string $message, ?string $route = null): Response
    {
        $route = $route ?? 'dashboard';
        return redirect()->route($route)->with('permission-error', $message);
    }
}
