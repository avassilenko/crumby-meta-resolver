Installation:
-------------
```
> composer require crumby/meta-resolver:"dev-master"
> php artisan vendor:publish --provider="Crumby\CanonicalHreflang\CanonicalHreflangServiceProvider" --tag=config


Register service and facade:
----------------------------
File: config/app.php
```
'providers' => [
    ......................
    'Crumby\CanonicalHreflang\CanonicalHreflangServiceProvider',
    ........................
 ];
 
 'aliases' => [ 
    ......................
    'Canonicalhreflang' => 'Crumby\CanonicalHreflang\Facades\CanonicalHreflang',
    ......................
 ];
```

Create Resolver classes:
------------------------
To fill Schema.org JSON-LD  structure on your page you will need to create class which can extract data from current content.
The class needs to implement interface Crumby\MetaResolver\Contracts\MetaResolver.php
```
<?php
namespace App\Resolvers;
use Crumby\MetaResolver\Contracts\MetaResolver as MetaResolver;

class CreativeWorkResolver implements MetaResolver {
    const IRI_FRAGMENT = '#creative';
    protected $service;
    public function __construct($service) {
        $this->service = $service;
    }
    
    public function type() {
        return 'CreativeWork';
    }
    
    public function hasPart() {
        return [
            "mainEntity" => [
                "author" => "Person" , 
                "publisher" => "Organization"
                ], 
            "breadcrumb" => "BreadcrumbList"
            ];
    }
    
    public function title() {
        return $this->service->title();
    }

    public function description() {
        return $this->service->description();
    }
    
    public function publishedAt() {
        return $this->service->content()->created_at->format('Y-m-d');
    }
    
    public function modifiedAt() {
        return $this->service->content()->updated_at->format('Y-m-d');
    }
    
    public function image() {
        return false;
    }
    
    public function url() {
        return $this->service->url();
    }
    
    public function iri() {
        return $this->url() . '/' . self::IRI_FRAGMENT;
    }
    
    public function schema($includeContext = true) {
        return [
            "@context" => "http://schema.org",
            "@type" => "WebPage",
            "url" => $this->url(),
            "isPartOf" => [
                "@type" => "WebSite",
                "@id" => \Request::root()
             ],
            "mainEntity" => [    
                "@type" => $this->type(),
                "@id" => $this->iri(),
                "mainEntityOfPage" => [
                    "@type" => "WebPage",
                    "@id" => $this->url(),
                ],
                "url" => $this->url(),
                "headline" => $this->title(),
                "description" => $this->description(),
                "dateCreated" => $this->publishedAt(),
                "dateModified" => $this->modifiedAt(),
            ],
        ];
    }
}
```
   
Configuration:
--------------
Map Schema.org type to resolver class:
File config/meta-resolver.php 
```
return [
        .........................
        'ContactPoint' => 'App\Resolvers\ContactPointResolver',
        'CreativeWork' => 'App\Resolvers\CreativeWorkResolver'
        ........................
];
```

Example: 
--------
Main method is \MetaResolver::addMeta().
 - add to your controller
```
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function article(ContentPackage $package, ContentItem $article) {
        \MetaResolver::addMeta(new CreativeWorkResolver($article), $article);
        $collection = new FrontPackages();
        return view('pages.package', ['collection' => $collection]);
    }
```  
 
 - use in you Blade template 
{{ $MetaResolver }}
