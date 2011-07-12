<?php
/**
 * Elgg Translation:
 * Es un objecto que está relacionado con otro y del cual será su traducción 
 *
 */
class ElggTranslation extends ElggObject{
	
	public static $languages = array(
		"aa", "ab", "af", "am", "ar", "as", "ay", "az", "ba", "be", "bg", "bh", "bi", "bn", "bo", "br",	"ca", "co", "cs", "cy",	"da",
		"de", "dz", "el", "en", "eo", "es", "et", "eu", "fa", "fi", "fj", "fo", "fr", "fy", "ga", "gd", "gl", "gn", "gu", "he", "ha",
		"hi", "hr", "hu", "hy", "ia", "id", "ie", "ik",	/*"i,*/"is", "it", "iu", "iw", "ja", "ji", "jw", "ka", "kk", "kl", "km", "kn",
		"ko", "ks", "ku", "ky", "la", "ln", "lo", "lt", "lv", "mg", "mi", "mk", "ml", "mn", "mo", "mr", "ms", "mt", "my", "na", "ne",
		"nl", "no", "oc", "om", "or", "pa", "pl", "ps", "pt", "qu", "rm", "rn", "ro", "ru", "rw", "sa", "sd", "sg", "sh", "si", "sk",
		"sl", "sm", "sn", "so", "sq", "sr", "ss", "st", "su", "sv", "sw", "ta", "te", "tg", "th", "ti", "tk", "tl", "tn", "to", "tr",
		"ts", "tt", "tw", "ug", "uk", "ur", "uz", "vi", "vo", "wo", "xh",/*"y,*/"yi", "yo", "za", "zh",	"zu",

	);
	
	/**
	* Initialize some defaults values for the entity.
 	*/	
	protected function initializeAttributes(){
		parent::initializeAttributes();	
	}
	
	public function getLanguageCodes(){
		return $languages;
	}

	/**
	* Set the language of a Translation
	*/	
	public function setLanguage($language){
		$this->language = $language;
	}
	
	/*
	* Get the language of a Translation
	*/
	public function getLanguage(){
		return $this->language;
	}	

	/**
	* Save a tranlation:
	* 	Look if a translation already exists with this language, if it forward to edit this one, or maybe just to the view of this one.
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
                $blog_guid = $this->getGUID();
		error_log("ADDING TRANSLATION TO " . $this->guid . " WITH " . $translation_guid); 
		
		/*if(!$this->isTranslation()){
			add_entity_relationship($blog_guid, "translation", $blog_guid);
		}*/

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
		$entities = elgg_get_entities_from_relationship(array(
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

	/** Overload delete method and delete all translations for an entity
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

	//See in relations if has some translations
	public function hasTranslations(){
		$translations= FALSE;
		error_log("TIENE TRADUCCIONES");
		$relationships = get_entity_relationships($this->getGUID());
		var_dump($relationships);
		foreach($relationships as $relation){
			if($relation->relationship == 'translation'){
				$translations = TRUE;
				error_log("SI");
			}
		}
		if($translations){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	//See in the relations if is a translation of other blog
	public function isTranslation(){
		$translation = FALSE;
		error_log("ES TRADUCCION");
		$relationships = get_entity_relationships($this->getGUID(), TRUE);
		var_dump($relationships);
		foreach($relationships as $relation){
			if($relation->relationship == 'translation' && $relation->guid_two == $this->getGUID()){
				error_log("LO ES");
				$translation = TRUE;
			}
		}
		if($translation){
			return TRUE;
		}else{
			return FALSE;
		}
	}

}
