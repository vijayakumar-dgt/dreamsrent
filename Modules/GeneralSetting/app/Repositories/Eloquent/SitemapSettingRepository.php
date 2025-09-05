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

    public function generateSitemap()
    {
        $result = '';

        try {
            $urls = SitemapUrl::all();
            if ($urls->isEmpty()) {
                // $result stays '' and will be returned at the end
            } else {
                $sitemap = Sitemap::create();
                foreach ($urls as $item) {
                    $u = $item->url;
                    if ($u) {
                        $sitemap->add(
                            Url::create($u)
                                ->setLastModificationDate(now())
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                ->setPriority(0.8)
                        );
                    }
                }

                $sitemapFolder = public_path('sitemaps');
                if (!file_exists($sitemapFolder) && !mkdir($sitemapFolder, 0777, true) && !is_dir($sitemapFolder)) {
                    // failed to create/ensure folder -> keep $result = ''
                } else {
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

                    if (file_exists($fullPath)) {
                        $latestUrl = SitemapUrl::orderByDesc('id')->first();
                        if ($latestUrl) {
                            $latestUrl->update(['sitemap_path' => $relativePath]);
                        }
                        $result = $relativePath;
                    }
                }
            }
        } catch (\Throwable $e) {
            // keep $result as '' on error (same behavior as before)
        }

        return $result;
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
