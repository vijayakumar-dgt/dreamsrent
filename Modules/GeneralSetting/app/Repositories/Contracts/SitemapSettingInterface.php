<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface SitemapSettingInterface
{
    public function index();
    public function store(array $data);
    public function generateSitemap();
    public function getSitemapUrls(array $filters);
    public function deleteSitemapUrl(int $id);
}
