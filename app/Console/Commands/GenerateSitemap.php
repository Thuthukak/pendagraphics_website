<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Spatie\Sitemap\Sitemap;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('weekly'))
            ->add(Url::create('/about-us')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/services/web-design')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/services/graphic-design')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/services/product-design')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/services/identity-design')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/services/e-commerce')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/services/digital-marketing')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/faq')->setPriority(0.7)->setChangeFrequency('monthly'))
            ->add(Url::create('/contact-us')->setPriority(0.7)->setChangeFrequency('monthly'))
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated!');
    }
}