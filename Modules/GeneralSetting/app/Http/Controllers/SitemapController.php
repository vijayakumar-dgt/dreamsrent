<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\GeneralSetting\Models\SitemapUrl;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

use function PHPUnit\Framework\fileExists;

class SitemapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():View
    {
        return view('generalsetting::other_settings.sitemap');
    }

    public function store():JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
            'id' => ['nullable', 'exists:sitemap_urls,id'],
            'url' => [
                'required',
                'string',
                'max:200',
                request()->id
                    ? 'unique:sitemap_urls,url,' . request()->id . ',id'
                    : 'unique:sitemap_urls,url',
                'regex:/^(https?:\/\/)(localhost|(\d{1,3}\.){3}\d{1,3}|([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}))(:\d+)?(\/.*)?$/'
            ]

            ],
            [
            'url.unique' => __('admin.general_settings.url_added'),
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $validator->errors()->toArray(),
                'message' => __('admin.general_settings.invalid'),
            ], 422);
        }

        try {
            $sitemap = new SitemapUrl();
            $sitemap->url = request()->url;
            $sitemap->save();
            $this->generateSitemap();
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' =>  __('admin.general_settings.sitemap_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => __('admin.general_settings.retrived_error'),
                'error' => $th->getMessage()
            ], 422);
        }
    }

    public function generateSitemap()
    {
        try {
            $urls = SitemapUrl::all();
            if ($urls->isEmpty()) {
                return;
            }

            $sitemap = Sitemap::create();
            foreach ($urls as $url) {
                $sitemap->add(
                    Url::create($url->url)
                        ->setLastModificationDate(now())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.8)
                );
            }

            $sitemapFolder = public_path('sitemaps');
            if (!file_exists($sitemapFolder) && !mkdir($sitemapFolder, 0777, true) && !is_dir($sitemapFolder)) {
                return;
            }
            $relativePath = 'sitemaps/sitemap-' . now()->format('YmdHis') . '.xml';
            $fullPath = public_path($relativePath);
            $sitemap->writeToFile($fullPath);
            if (!file_exists($fullPath)) {
                return;
            }
            $latestUrl = SitemapUrl::latest()->first();
            if ($latestUrl) {
                $latestUrl->update(['sitemap_path' => $relativePath]);
            }

            return $relativePath;
        } catch (\Throwable $e) {
            return;
        }
    }

    public function getSitemapUrls(Request $request):JsonResponse
    {
        $pageLength = $request->input('length', 10);
        $offset = $request->input('start', 0);

        $sitemapUrlsQuery = SitemapUrl::query();

        if ($request->filled('keyword')) {
            $sitemapUrlsQuery->where('url', 'like', '%' . $request->input('keyword') . '%');
        }

        $filteredRecords = $sitemapUrlsQuery->count();
        $totalRecords = SitemapUrl::count();

        $sitemapUrls = $sitemapUrlsQuery->orderBy('id', 'desc')
            ->skip($offset)
            ->take($pageLength)
            ->get()
            ->map(function ($sitemapUrl) {
                return [
                    'filePath' => !empty($sitemapUrl->sitemap_path) &&
                        file_exists(public_path($sitemapUrl->sitemap_path))
                            ? asset($sitemapUrl->sitemap_path)
                            : '',
                    'url' => $sitemapUrl->url,
                    'sitemap_path' => $sitemapUrl->sitemap_path,
                    'id' => $sitemapUrl->id,
                ];
            });

        return response()->json([
            'draw' => $request->input('draw', 0),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $sitemapUrls,
        ]);
    }

    public function deleteSitemapUrl(Request $request):JsonResponse
    {
        $sitemapUrl = SitemapUrl::find($request->id);
        try {
            if (!empty($sitemapUrl->sitemap_path) && file_exists(public_path($sitemapUrl->sitemap_path))) {
                unlink(public_path($sitemapUrl->sitemap_path));
            }
            $sitemapUrl->delete();
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.sitemap_url_delete'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $th->getMessage()
            ], 422);
        }
    }
}
