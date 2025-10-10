<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Models\SitemapUrl;
use Modules\GeneralSetting\Repositories\Contracts\SitemapSettingInterface;
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

    public function generateSitemap(): string
    {
        try {
            $urls = SitemapUrl::all();
            if ($urls->isEmpty()) {
                return '';
            }

            $sitemap = $this->createSitemap($urls);

            $sitemapFolder = public_path('sitemaps');
            if (!$this->ensureFolderExists($sitemapFolder)) {
                return '';
            }

            $this->archivePreviousSitemap();

            $relativePath = 'sitemaps/sitemap.xml';
            $fullPath = public_path($relativePath);
            $sitemap->writeToFile($fullPath);

            if (!file_exists($fullPath)) {
                return '';
            }

            $this->updateLatestSitemapPath($relativePath);

            return $relativePath;
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Create sitemap object from URLs
     */
    private function createSitemap($urls)
    {
        $sitemap = Sitemap::create();
        foreach ($urls as $item) {
            if ($item->url) {
                $sitemap->add(
                    Url::create($item->url)
                        ->setLastModificationDate(now())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.8)
                );
            }
        }
        return $sitemap;
    }

    /**
     * Ensure folder exists
     */
    private function ensureFolderExists(string $folder): bool
    {
        return file_exists($folder) || mkdir($folder, 0777, true) || is_dir($folder);
    }

    /**
     * Archive the previous sitemap (skip the latest one)
     */
    private function archivePreviousSitemap(): void
    {
        $lastBeforeSitemap = SitemapUrl::orderByDesc('id')->skip(1)->first();
        if (!$lastBeforeSitemap || !$lastBeforeSitemap->sitemap_path) {
            return;
        }

        $oldPath = public_path($lastBeforeSitemap->sitemap_path);
        if (!file_exists($oldPath)) {
            return;
        }

        $newFilename = 'sitemaps/sitemap-' . date('Y-m-d-H-i-s') . '-' . rand(1000, 9999) . '.xml';
        $newFullPath = public_path($newFilename);

        if (rename($oldPath, $newFullPath)) {
            $lastBeforeSitemap->sitemap_path = $newFilename;
            $lastBeforeSitemap->save();
        }
    }

    /**
     * Update the latest sitemap path in DB
     */
    private function updateLatestSitemapPath(string $relativePath): void
    {
        $latestUrl = SitemapUrl::orderByDesc('id')->first();
        if ($latestUrl) {
            $latestUrl->update(['sitemap_path' => $relativePath]);
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
                    'url'          => $sitemapUrl->url,
                    'sitemap_path' => $sitemapUrl->sitemap_path,
                    'id'           => $sitemapUrl->id,
                ];
            });

        return [
            'draw'            => $filters['draw'] ?? 0,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $sitemapUrls,
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
