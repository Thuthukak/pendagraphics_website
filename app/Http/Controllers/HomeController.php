<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{

    public function Home()
    {
        // Define hero images for server-side rendering
        $heroImages = [
            'left' => asset('assets/images/left.png'),
            'top' => asset('assets/images/top.png'),
            'right' => asset('assets/images/right.png'),
        ];

        $aboutImage = asset('assets/images/p-logo.png');

        // Define service icons for server-side rendering
        $serviceIcons = [
            'web_development' => asset('assets/gifs/worldwide.gif'),
            'product_design' => asset('assets/gifs/dairy-products.gif'),
            'graphic_design' => asset('assets/gifs/design.gif'),
            'identity_design' => asset('assets/gifs/passport.gif'),
            'ecommerce' => asset('assets/gifs/online-supermarket.gif'),
            'digital_marketing' => asset('assets/gifs/responsive-design.gif'),
        ];

        // SEO and Open Graph data
        $seoData = [
            'title' => 'Professional Web Design & Digital Marketing Services',
            'description' => 'We are a team of passionate designers and developers dedicated to creating exceptional digital experiences. Specializing in web development, graphic design, branding, and digital marketing.',
            'keywords' => 'web design, web development, graphic design, digital marketing, branding, logo design, SEO, e-commerce',
            'og_title' => 'Empowering Your Brand with Stunning Designs & Websites',
            'og_description' => 'Professional web design and development services. Create stunning websites, powerful branding, and effective digital marketing campaigns.',
            'og_image' => asset('assets/images/penda_logo2.png'), // Create this image (1200x630px)
            'og_url' => url('/'),
            'twitter_card' => 'summary_large_image',
            'canonical_url' => url('/'),
        ];

        // Services data for better SEO
        $services = [
            [
                'title' => 'Web Development',
                'description' => 'We incorporate the latest design trends, intuitive navigation, and engaging visuals to ensure that your website stands out.',
                'url' => '/services/web-design',
                'icon' => $serviceIcons['web_development'],
            ],
            [
                'title' => 'Product Design',
                'description' => 'We specialize in turning your innovative ideas into tangible products that resonate with your audience and drive success.',
                'url' => '/services/product-design',
                'icon' => $serviceIcons['product_design'],
            ],
            [
                'title' => 'Graphic Design',
                'description' => 'Posters, flyers, and banners, brochures, business cards, infographics, social media graphics, and packaging design.',
                'url' => '/services/graphic-design',
                'icon' => $serviceIcons['graphic_design'],
            ],
            [
                'title' => 'Identity Design',
                'description' => 'We help develop a strong brand identity through logo design, brand guidelines, brand strategy, and brand messaging.',
                'url' => '/services/identity-design',
                'icon' => $serviceIcons['identity_design'],
            ],
            [
                'title' => 'E-Commerce Solutions',
                'description' => 'We provide e-commerce design and development services, including online store setup, product listing, payment integration, and order management.',
                'url' => '/services/e-commerce',
                'icon' => $serviceIcons['ecommerce'],
            ],
            [
                'title' => 'Digital Marketing',
                'description' => 'We provide digital marketing services, including search engine optimization (SEO), social media marketing, content marketing, email marketing, etc.',
                'url' => '/services/digital-marketing',
                'icon' => $serviceIcons['digital_marketing'],
            ],
        ];

        // Structured Data for SEO (define after $services)
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Your Company Name',
            'description' => $seoData['description'],
            'url' => $seoData['og_url'],
            'logo' => $seoData['og_image'],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'availableLanguage' => 'English'
            ],
            'sameAs' => [
                // Add your social media URLs here
                // 'https://www.facebook.com/yourcompany',
                // 'https://www.twitter.com/yourcompany',
                // 'https://www.linkedin.com/company/yourcompany'
            ],
            'service' => array_map(function($service) {
                return [
                    '@type' => 'Service',
                    'name' => $service['title'],
                    'description' => $service['description']
                ];
            }, $services)
        ];

        return Inertia::render('Home', [
            'heroImages' => $heroImages,
            'aboutImage' => $aboutImage,
            'services' => $services,
            'seo' => $seoData,
            'structuredData' => json_encode($structuredData),
        ]);
    }


    public function FaqIndex () {
        return view('faq');
    }

    public function AboutUsIndex () {
        return view('about-us');
    }

    public function HomeIndex () {
        return view('contact-us');
    }
}
