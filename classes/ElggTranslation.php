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
		
	}
	
	public function setLanguage($language){
		$this->language = $language;
	}
	
	public function getLanguage(){
		return $this->language;
	}	

	/**
	* Save a tranlation
	*
	*/
	public function save(){
		if(!parent::save()){
			return false;
		}
		$this->setMetadata('language',$this->language);
		return true;
	}	
	
	/**
        * Add a translation to a blog
	* In order to add a translation we can do it in two ways.
	* It has to look for the entity that is being translated, see if is a translation of other
	* If is make this a translation of this one, if not make himself a translation of the first one.
	* 	- Saving in the same row the language of the translation
	*	- Creating other table with the codes of the languages
	*	
        */
        public function addTranslation($translation_guid){
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
			'relationship_guid' => $this->getGUID()
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
			if(elgg_instanceof($entity,'object','translation')){
				$entity->delete();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

}
