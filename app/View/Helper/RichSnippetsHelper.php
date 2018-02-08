<?php
/**
 * Rich snippets helper v1.0
 * By: Aleksey Vovk
 */
App::uses('AppHelper', 'View/Helper');

class RichSnippetsHelper extends AppHelper {

    public static function create($richSnippetName){
        $richSnippetName .= 'RichSnippet';
        if(class_exists($richSnippetName)){
            return new $richSnippetName;
        }else{
            throw new Exception($richSnippetName . ' Rich snippet does not exist');
        }
    }

    public static function convertDateTime($dateTimeString){
        return date('Y-m-d\TH:iP', strtotime($dateTimeString));
    }

}

class RichSnippet {
    protected $values = array();
    protected $children = array();

    public function setVal($name,$val){
        if($val){
            if(in_array($name,$this->fields)){
                $this->values[$name] = $val;
            }else{
                throw new Exception(get_class($this) . ' does not have the property ' . $name);
            }
        }
    }

    public function addChild($property, $richSnippetName){
        $child = RichSnippetsHelper::create($richSnippetName);
        if(!isset($this->children[$property])) $this->children[$property] = array();
        $this->children[$property][] = $child;
        return $child;
    }

    public function getArray(){
        return $this->getJSON(true);
    }

    public function getJSON($isChild = false){
        //remove context if is child
        if($isChild && isset($this->defaults['@context'])) unset($this->defaults['@context']);

        //create the json
        $json = $this->defaults + $this->values;
        foreach($this->children as $property => $children){
            if(count($children) == 1){
                $json[$property] = $children[0]->getJSON(true);
            }else{
                $json[$property] = array();
                foreach($children as $child){
                    $json[$property][] = $child->getJSON(true);
                }
            }
        }

        //if root then transform into json
        if(!$isChild){
            return "<script type='application/ld+json'>\n" . json_encode($json) ."\n</script>";
        }

        return $json;
    }
}
class WebSiteRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@context' => 'https://schema.org',
        '@type' => 'webSite'
    );
    protected $fields = array('name','url');

}

class OrganizationRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@context' => 'http://schema.org',
        '@type' => 'Organization'
    );
    protected $fields = array('brand','name','url','logo','sameAs');
}

class BreadcrumbListRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@context' => 'http://schema.org',
        '@type' => 'BreadcrumbList'
    );
    protected $fields = array('itemListElement');
}

class ItemListRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@context' => 'http://schema.org',
        '@type' => 'ItemList'
    );
    protected $fields = array('url', 'numberOfItems', 'itemListElement');
}

class LocalBusinessRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@context' => 'http://schema.org',
        '@type' => 'LocalBusiness'
    );
    protected $fields = array('additionalType','name','url','logo','sameAs','currenciesAccepted','image');
}

class ProductRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@context'=> 'https://schema.org',
        '@type' => 'Product',
    );
    protected $fields = array('additionalType','name','description','url','image','review','price','priceCurrency','position', 'availability');
}
class ReviewRichSnippet extends RichSnippet{
    protected $defaults = array(
        "@context"=> "https://schema.org",
        "@type"=> "Review"
    );
    protected $fields = array('datePublished','reviewBody');
}


//----- parts --------
//--------------------
class SearchActionRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'SearchAction'
    );
    protected $fields = array('target','query-input');

}

class PersonRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'Person'
    );
    protected $fields = array('name','sameAs');
}

class PostalAddressRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'PostalAddress'
    );
    protected $fields = array('streetAddress','addressLocality','addressRegion','postalCode');
}
class OfferRichSnippet extends RichSnippet
{
    protected $defaults = array(
        '@type' => 'Offer'
    );
    protected $fields = array('price','priceCurrency','availability','itemOffered');
}
class PaymentMethodRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'PaymentMethod'
    );
    protected  $fields = array('name');
}
class AggregateRatingRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'AggregateRating'
    );
    protected $fields = array('ratingValue','reviewCount');
}
class RatingRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'Rating'
    );
    protected $fields = array('ratingValue','bestRating','worstRating');
}

class Review2RichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'Review'
    );
    protected $fields = array('author', 'datePublished', 'description', 'name');
}
class ContactPointRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'ContactPoint'
    );
    protected $fields = array('email', 'telephone', 'contactType', 'contactOption', 'areaServed');
}
class ListItemRichSnippet extends RichSnippet{
    protected $defaults = array(
        '@type' => 'ListItem'
    );
    protected $fields = array('position','item','url');
}
class ItemRichSnippet extends RichSnippet{
    protected $defaults = array();
    protected $fields = array('@id', 'name');
}

