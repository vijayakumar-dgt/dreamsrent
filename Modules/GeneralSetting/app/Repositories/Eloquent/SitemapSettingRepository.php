<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Repositories\Contracts\SitemapSettingInterface;
use Modules\GeneralSetting\Models\SitemapUrl;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapSettingRepository implements SitemapSettingInterface
{
    public function index()
    {
        return view('generalsetting::other_settings.sitemap');
    }

    public function store(array $data)
    {
        $sitemap = new SitemapUrl();
        $sitemap->url = $data['url'];
        $sitemap->save();
        $this->generateSitemap();
        
        return $sitemap;
    }

    public function generateSitemap()
    {
        try {
            $urls = SitemapUrl::all();
            if ($urls->isEmpty()) {
                return '';
            }

            $sitemap = Sitemap::create();
            foreach ($urls as $url) {
                $url = $url->url;
                if ($url) {
                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate(now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.8)
                    );
                }
            }

            $sitemapFolder = public_path('sitemaps');
            if (!file_exists($sitemapFolder) && !mkdir($sitemapFolder, 0777, true) && !is_dir($sitemapFolder)) {
                return '';
            }
            $lastBeforeSitemap = SitemapUrl::orderByDesc('id')->skip(1)->first();

            if ($lastBeforeSitemap && $lastBeforeSitemap->sitemap_path) {
                $oldPath = public_path($lastBeforeSitemap->sitemap_path);
                if (file_exists($oldPath)) {
                    $newFilename = 'sitemaps/sitemap-' . date('Y-m-d-H-i-s') . '-' . rand(1000, 9999) . '.xml';
                    $newFullPath = public_path($newFilename);

                    if (rename($oldPath, $newFullPath)) {
                        $lastBeforeSitemap->sitemap_path = $newFilename;
                        $lastBeforeSitemap->save();
                    }
                }
            }
            
            $relativePath = 'sitemaps/sitemap.xml';
            $fullPath = public_path($relativePath);
            $sitemap->writeToFile($fullPath);
            if (!file_exists($fullPath)) {
                return '';
            }
            
            $latestUrl = SitemapUrl::orderByDesc('id')->first();
            if ($latestUrl) {
                $latestUrl->update(['sitemap_path' => $relativePath]);
            }

            return $relativePath;
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function getSitemapUrls(array $filters)
    {
        $pageLength = $filters['length'] ?? 10;
        $offset = $filters['start'] ?? 0;

        $sitemapUrlsQuery = SitemapUrl::query();

        if (!empty($filters['keyword'])) {
            $sitemapUrlsQuery->where('url', 'like', '%' . $filters['keyword'] . '%');
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

        return [
            'draw' => $filters['draw'] ?? 0,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $sitemapUrls,
        ];
    }

    public function deleteSitemapUrl(int $id)
    {
        $sitemapUrl = SitemapUrl::findOrFail($id);
        
        if (!empty($sitemapUrl->sitemap_path) && file_exists(public_path($sitemapUrl->sitemap_path))) {
            unlink(public_path($sitemapUrl->sitemap_path));
        }
        
        return $sitemapUrl->delete();
    }
}