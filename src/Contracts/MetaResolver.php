<?php
namespace Crumby\MetaResolver\Contracts;
/**
 *
 * @author Andrei Vassilenko <avassilenko2@gmail.com>
 */
interface  MetaResolver {
    /**
     * returns array representing Shema.org types
     * @param boolean $includeContext To include or not "@context" property. Default is true.
     */
    public function schema($includeContext = true);
    
    /**
     * Returns string representing page description. Be used in <meta name="description".
     * Return false if not required.
     */
    public function description();
    
    /**
     * Returns string representing page title. Be used in <title>
     * Return false if not required.
     */
    public function title();
    
    /**
     * Returns string representing of page main image. 
     * Return false if not required.
     */
    public function image();
    
    /**
     * returns Shema.org type
     */
    public function type();
    /**
     * returns array [property => Shema.org type] 
     */
    public function hasPart();
    
    /*
     * returns internatiolized resource id 
     * @return string|false  If is not defined false
     */
    public function iri();
    
    /*
     * returns the object url
     * @return string|false  If is not defined false
     */
    public function url();
}
