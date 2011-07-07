<?php
/**
 * Elgg Translation:
 * Es un objecto que está relacionado con otro y del cual será su traducción 
 *
 */
class ElggTranslation extends ElggObject{
	
	/**
	* Initialize some defaults values for the entity.
 	*/	
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
	* 	- Receives the language of the translation we want to get.
	*	- Return the entity of the translation if succeed.
	* 	- Return false if doesnt not exist.
	*/
	public function getTranslation($language){
		$entities = get_entities_from_relationship(array(
			'relationship' => "translation",
			'relationship_guid' => $this
        	));
		foreach($entities as $entitie){
			if($entitie->language == $language){
				return $entitie;
			}else{
				return false;
			}
		}
	}


	/**
	* Delete a translation
	*	- Receives the language of the translation we want to delete.
  	*	- Return false if fails.
	*/
	public function deleteTranslation($language){
		if($entity = getTranslation($language)){
			//if($entity is instanceof ElggBlog){
				$entity->delete();
			//}else{
			//	return false;
			//}
		}else{
			return false;
		}
	}


}
