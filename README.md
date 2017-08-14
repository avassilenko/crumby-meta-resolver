1. Register service and facade. 
File: config/app.php

'providers' => [
    ......................
    'Crumby\MetaResolver\MetaResolverServiceProvider',
    ........................
 ];
 
 'aliases' => [ 
    ......................
    'MetaResolver' => 'Crumby\MetaResolver\Facades\MetaResolver',
    ......................
 ];
      
2. Map Schema.org type to resolver class in config/meta-resolver.php 
return [
        .........................
        'ContactPoint' => 'App\Resolvers\ContactPointResolver',
        ........................
];

3. Implement interface Crumby\MetaResolver\Contracts\MetaResolver.php
in file, for example:
app/Resolvers/ArticleResolver.php

4.  Add resolver class in Controller class action. It will be resolved then method
 public function hasPart() {
        return ["author" => "Person"];
    }
make call to hasPart() and will add to property "author" Schema.org type Person. Then resolver will use map and inject 
'App\Resolvers\ContactPointResolver'

5. make sure the App\Resolvers\ArticleResolver, App\Resolvers\ContactPointResolver .. classes are exist in autoloader
    
6. you may add breadcrumbs to json+ld structured data: 
    Implement Resolver:
    app/Resolvers/BreadcrumbListResolver.php

        namespace App\Resolvers;
        use Crumby\MetaResolver\Contracts\MetaResolver as MetaResolver;
        class BreadcrumbListResolver implements MetaResolver {
            protected $service;
            public function __construct($service) {
                $this->service = $service;
            }
            public function type() {
                return 'BreadcrumbList';
            }
            public function hasPart() {
                return false;
            }
            public function toArray() {
                return \Breadcrumbs::toArray();
            }
        }    
    Add the structured data property to parent Resolver ArticleResolver
    app/Resolvers/ArticleResolver.php
    class ArticleResolver implements MetaResolver {
            ........................
            public function hasPart() {
                return ["author" => "Person", "breadcrumb" => "BreadcrumbList"];
            }
           ..................
        }    
    
    
7. use in you Blade template 
{{ $MetaResolver }}

8. API you need to implement in your resolver
    /**
     * returns array representing Shema.org types
     */
    public function schema();
    
    /**
     * returns string representing page description. Be used in <meta name="description".
     */
    public function description();
    
    /**
     * returns string representing page title. Be used in <title>
     */
    public function title();    