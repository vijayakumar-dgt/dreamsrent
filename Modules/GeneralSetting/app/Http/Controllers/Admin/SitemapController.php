<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\SitemapSettingRequest;
use Modules\GeneralSetting\Repositories\Contracts\SitemapSettingInterface;

class SitemapController extends Controller
{
    protected $sitemapSetting;

    public function __construct(SitemapSettingInterface $sitemapSetting)
    {
        $this->sitemapSetting = $sitemapSetting;
    }

    public function index(): View
    {
        return $this->sitemapSetting->index();
    }

    public function store(SitemapSettingRequest $request): JsonResponse
    {
        try {
            $this->sitemapSetting->store($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.sitemap_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.retrived_error'),
                'error'   => $th->getMessage()
            ], 422);
        }
    }

    public function generateSitemap(): JsonResponse
    {
        $result = $this->sitemapSetting->generateSitemap();

        return response()->json([
            'status'  => !empty($result) ? 'success' : 'error',
            'message' => !empty($result)
                ? __('admin.general_settings.sitemap_generated')
                : __('admin.general_settings.sitemap_generation_failed')
        ]);
    }

    public function getSitemapUrls(): JsonResponse
    {
        $data = $this->sitemapSetting->getSitemapUrls(request()->all());

        return response()->json($data);
    }

    public function deleteSitemapUrl(): JsonResponse
    {
        try {
            $this->sitemapSetting->deleteSitemapUrl(request()->id);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.sitemap_url_delete'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $th->getMessage()
            ], 422);
        }
    }
}
