<?php
namespace Crumby\MetaResolver;
/**
 *
 * @author Andrei Vassilenko <avassilenko2@gmail.com>
 */
class MetaResolver {
    /*
     * To load Schema.org structured data types map to classes
     * @var
     */
    protected $resolversMap;
    
    /**
     * resolved schema.org structure;
     */
    protected $schema;
    
    /**
     * resolved title;
     */
    protected $title;
    
     /**
     * resolved description;
     */
    protected $description;
    
     /**
     * resolved image;
     */
    protected $image;
    
    public function __construct() {
        $this->loadConfig();
    }
    
    /**
     * Load configuration
     */
    public function loadConfig() {
        $this->resolversMap = config('crumby-crumbs.meta-resolver');
    }
    
    /**
     * 
     * @param $schema Schema.org type
     * @return string | boolean Class name presenting the type
     */
    public function getResolverBySchema($schema) {
        if (isset($this->resolversMap[$schema])) {
            return $this->resolversMap[$schema];
        }
        return false;
    }
    
    /**
     * Adds Schema.org meta data to head of the page. 
     * Adds page title and description to the page head. 
     * 
     * @param mixed $resolver object of Crumby\MetaResolver\Facades\MetaResolver class to resolve Schema.org type
     * @param type $param parameter passed to the object constructor. Can be passed to child object
     */
    public function addMeta($resolver, $param = null) {
        $this->schema = $resolver->schema();
        $this->description = $resolver->description();
        $this->title = $resolver->title();
        $this->image = $resolver->image();
        if ($propertiesToResolve = $resolver->hasPart()) {
            $this->buildSchemaDependencies($propertiesToResolve, $param);
            $this->schema = array_merge_recursive($this->schema, $propertiesToResolve);
        } 
    }
   
    
    /**
     * Builds Schema.org entyties hierarchy. Resolves properties in the page content.   
     * @param array $dependencies Schema.org entyties hierarchy returned by method hasPart()
     * @param mixed $param argument passed to the page top level resolver
     */
    protected function buildSchemaDependencies(&$dependencies, $param=null) {
        foreach($dependencies as $property => &$schema) {
            if (is_array($schema)) {
                $this->buildSchemaDependencies($schema, $param);
            }
            else {
                if($resolverClass = $this->getResolverBySchema($schema)) {
                    $schema = (new $resolverClass($param))->schema();
                }
            }
        }
    }
    
    /**
     * Returns resolved structured schema.org data
     * @return []
     */
    public function schema() {
        return $this->schema;
    }
    
    /**
     * Returns resolved page title
     * @return string|false Returns string if exists, false is is not set
     */
    public function title() {
        return $this->title;
    }
    
    /**
     * Returns resolved page description
     * @return string|false Returns string if exists, false is is not set
     */
    public function description() {
        return $this->description;
    }
    
    public function __toString() {
        $script = '';
        if ($title = $this->title()) {
            $script .= '<title>';
            $script .= $title;
            $script .= '</title>';
        }
        if ($description = $this->description()) {
            $script .= '<meta name="description" content="';
            $script .= $description;
            $script .= '" />';
        }
        if ($schema = $this->schema()) {
            $script .= '<script type="application/ld+json">';
            $script .= json_encode($schema, JSON_UNESCAPED_SLASHES);
            $script .= '</script>';
        }
        return $script;
    }
    
}
