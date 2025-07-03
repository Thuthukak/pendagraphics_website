<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends BaseController
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

        $serviceIcons = [
            'web_development' => asset('assets/gifs/worldwide.gif'),
            'product_design' => asset('assets/gifs/dairy-products.gif'),
            'graphic_design' => asset('assets/gifs/design.gif'),
            'identity_design' => asset('assets/gifs/passport.gif'),
            'ecommerce' => asset('assets/gifs/online-supermarket.gif'),
            'digital_marketing' => asset('assets/gifs/responsive-design.gif'),
        ];

        // Customize SEO data for home page
        $seoData = $this->mergeSeoData([
            'description' => 'We are a team of passionate designers and developers dedicated to creating exceptional digital experiences. Specializing in web development, graphic design, branding, and digital marketing.',
            'og_description' => 'Professional web design and development services. Create stunning websites, powerful branding, and effective digital marketing campaigns.',
        ]);

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

        // Structured Data for SEO
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
                'https://web.facebook.com/Penda.graphix',
                'https://www.instagram.com/penda_graphics/',
                'https://www.youtube.com/@PendaGraphics',
                'https://www.tiktok.com/@pendagraphics?_t=ZM-8xCbzWLUJ7Q&_r=1',
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

         $seoData = $this->mergeSeoData([
            'description' => '',
            'og_description' => '',
        ]);

        return Inertia::render('Faq', [
            'seo' => $seoData, 
        ]);
    }

    public function AboutUsIndex () {

    $seoData = $this->mergeSeoData([
        // Basic SEO
        'title' => 'About Us | Digital Design & Development Agency',
        'description' => 'Discover Penda Graphics\' journey from boutique design studio to comprehensive digital solutions provider. Specializing in web development, branding, graphic design, and digital marketing since 2019.',
        'keywords' => 'about Penda Graphics, digital design agency, web development company, graphic design studio, branding experts, digital marketing agency, web applications, e-commerce solutions, UI/UX design, Laravel development, Vue.js, responsive web design, logo design, brand identity, Kempton Park, South Africa',
        'canonical_url' => url('/about-us'),
        
        // Open Graph (Facebook, LinkedIn, etc.)
        'og_title' => 'About Penda Graphics - Digital Design & Development Experts',
        'og_description' => 'From boutique design studio to full-service digital agency. Transforming brands through innovative design and technology since 2019. Web development, branding, and digital marketing specialists.',
        'og_image' => asset('assets/images/og-about-penda-graphics.jpg'), // Consider creating a specific OG image
        'og_url' => url('/about-us'),
        'og_type' => 'website',
        'og_site_name' => 'Penda Graphics',
        
        // Twitter Card
        'twitter_card' => 'summary_large_image',
        'twitter_title' => 'About Penda Graphics - Digital Design & Development Experts',
        'twitter_description' => 'From boutique design studio to full-service digital agency. Transforming brands through innovative design and technology since 2019.',
        'twitter_image' => asset('assets/images/twitter-about-penda-graphics.jpg'), // Consider creating a Twitter-optimized image
        
        // Additional SEO data
        'author' => 'Penda Graphics',
        'robots' => 'index, follow',
        'revisit_after' => '7 days',
        'language' => 'en-US',
        'geo_region' => 'ZA-GP',
        'geo_placename' => 'Kempton Park, Gauteng, South Africa',
        'geo_position' => '-26.1186;28.2294',
        'ICBM' => '-26.1186, 28.2294',
        
        // Schema.org structured data
        'schema_type' => 'Organization',
        'schema_name' => 'Penda Graphics',
        'schema_description' => 'Digital design and development agency specializing in web development, branding, graphic design, and digital marketing solutions.',
        'schema_url' => url('/'),
        'schema_logo' => asset('assets/images/penda-graphics-logo.png'),
        'schema_image' => asset('assets/images/penda-graphics-team.jpg'),
        'schema_address' => [
            'streetAddress' => 'Birch Acres',
            'addressLocality' => 'Kempton Park',
            'addressRegion' => 'Gauteng',
            'addressCountry' => 'ZA'
        ],
        'schema_contact' => [
            'telephone' => '+27738114652',
            'email' => 'info@pendagraphics.co.za'
        ],
        'schema_founded' => '2019',
        'schema_services' => [
            'Web Design & Development',
            'Graphic Design',
            'Brand Identity Design',
            'Digital Marketing',
            'Web Applications',
            'E-commerce Solutions'
        ],
        
        // Page-specific images
        'sec_img' => asset('assets/images/painted_p_logo.png'),
        'sec_img2' => asset('assets/images/approach.png'),
        
        // Breadcrumbs
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'About Us', 'url' => url('/about-us')]
        ],
        
        // Additional meta tags
        'theme_color' => '#005e91',
        'msapplication_TileColor' => '#005e91',
        'apple_mobile_web_app_capable' => 'yes',
        'apple_mobile_web_app_status_bar_style' => 'default',
        
        // Local business information
        'business_hours' => [
            'monday' => '08:00-17:00',
            'tuesday' => '08:00-17:00',
            'wednesday' => '08:00-17:00',
            'thursday' => '08:00-17:00',
            'friday' => '08:00-17:00',
            'saturday' => '09:00-13:00',
            'sunday' => 'closed'
        ],
        
        // Social media links
        'social_media' => [
            'facebook' => 'https://web.facebook.com/Penda.graphix',
            'instagram' => 'https://www.instagram.com/penda_graphics/',
        ],
        
        // Performance and technical SEO
        'preload_images' => [
            asset('assets/images/painted_p_logo.png'),
            asset('assets/images/approach.png')
        ],
        
        // Content freshness
        'last_modified' => now()->toISOString(),
        'published_time' => '2019-01-01T00:00:00+00:00',
        'modified_time' => now()->toISOString(),
    ]);
    
    return Inertia::render('AboutUS', [
        'seo' => $seoData,
    ]);
}

    public function ContactIndex() {
    // Customize SEO data for contact page
    $seoData = $this->mergeSeoData([
        'title' => 'Contact Us | Get in Touch with Penda Graphics',
        'description' => 'Contact Penda Graphics for professional web design, branding, and digital marketing services in South Africa. Call +27 73 811 4652 or email info@pendagraphics.co.za for a free consultation.',
        'keywords' => 'contact Penda Graphics, web design consultation, South Africa web design, Kempton Park web design, contact web designer, get quote web design, branding consultation, digital marketing contact',
        'og_title' => 'Contact Penda Graphics | Professional Web Design & Branding Services',
        'og_description' => 'Ready to elevate your brand? Contact our expert team for web design, branding, and digital marketing services. Located in Kempton Park, serving all of South Africa.',
        'og_type' => 'website',
        'og_url' => url('/contact'),
        'canonical_url' => url('/contact'),
        'twitter_title' => 'Contact Penda Graphics | Web Design & Digital Solutions',
        'twitter_description' => 'Get in touch with Penda Graphics for professional web design, branding, and digital marketing services in South Africa.',
    ]);

    // Structured Data for SEO (separate from seoData for cleaner organization)
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'mainEntity' => [
            '@type' => 'LocalBusiness',
            'name' => 'Penda Graphics',
            'description' => 'Professional web design, branding, and digital marketing services',
            'url' => config('app.url'),
            'telephone' => '+27738114652',
            'email' => 'info@pendagraphics.co.za',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Birch Acres',
                'addressRegion' => 'Gauteng',
                'addressCountry' => 'ZA',
                'postalCode' => '1618'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => -26.052120488886704,
                'longitude' => 28.182524335456343
            ],
            'openingHours' => 'Mo-Fr 09:00-17:00',
            'sameAs' => [
                'https://web.facebook.com/Penda.graphix',
                'https://www.instagram.com/penda_graphics/',
                'https://www.youtube.com/@PendaGraphics',
                'https://www.tiktok.com/@pendagraphics'
            ],
            'priceRange' => '$',
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'South Africa'
            ],
            'serviceType' => [
                'Web Design',
                'Web Development',
                'Branding',
                'Graphic Design',
                'Digital Marketing',
                'SEO Services'
            ]
        ]
    ];

    // Additional contact-specific data
    $contactInfo = [
        'phone' => '+27738114652',
        'email' => 'info@pendagraphics.co.za',
        'address' => 'Birch Acres, Kempton Park, Gauteng, South Africa',
        'business_hours' => 'Monday - Friday: 9am - 5pm'
    ];

    return Inertia::render('ContactUs', [
        'seo' => $seoData,
        'structuredData' => json_encode($structuredData),
        'contactInfo' => $contactInfo,
    ]);
}
}
