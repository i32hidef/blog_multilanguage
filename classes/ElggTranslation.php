<?php
/**
 * Elgg Translation:
 * Es un objecto que está relacionado con otro y del cual será su traducción 
 *
 */
class ElggTranslation extends ElggObject{
	
	protected function initializeAttributes(){
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = "translation";
		$this->attributes['language'] = NULL;
	}
	
	
	/**
        * Add a translation to a blog
	* In order to add a translation we can do it in two ways.
	* 	- Saving in the same row the language of the translation
	*	- Creating other table with the codes of the languages
	*	
        */
        public function addTranslation($translation_guid, $language){
		//error_log("BLOG TRANSLATION ADD TRANSLATION");
                $blog_guid = $this->getGUID();
		error_log("ADDING TRANSLATION TO " . $this->guid . " WITH " . $translation_guid); 

                if ($blog_guid == $translation_guid) {
                        return false;
                }
                if (!$translation = get_entity($translation_guid)) {
                        return false;
                }
                if (!$blog = get_entity($blog_guid)) {
                        return false;
                }
                if ((!($blog instanceof ElggBlog)) || (!($translation instanceof ElggBlog))) {
                        return false;
                }
                return add_entity_relationship($blog_guid, "translation", $translation_guid);
        }
	
	/**
	* Get a translation
	*/
}
