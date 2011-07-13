<?php
/**
 * Elgg Translation:
 * Create an ElggTranslation object designed to be extended by ElggBlog
 *
 */
class ElggTranslation extends ElggObject{

	/**
	 * Array with ISO 639-1 language codes
	 */	
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
	
	/**
	 * Return languages array
	 */
	public function getLanguageCodes(){
		return $languages;
	}

	/**
	 * Set language of a Translation
	 * @param string $language
	 */	
	public function setLanguage($language){
		$this->language = $language;
	}
	
	/*
	 * Get language of a Translation
	 * @return string 
	 */
	public function getLanguage(){
		return $this->language;
	}	

	/**
	 * Save a tranlation:
	 * 	Look if a translation already exists with this language, if it forward to edit this one, or maybe just to the view of this one.
	 * @return bool
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
 	 * @param string $translation_guid
	 * @return bool	
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
	 * Get a translation entity
	 * @param string $language
	 * @return Entity|false Depending on success
	 */
	public function getTranslation($language){
		$entities = elgg_get_entities_from_relationship(array(
			'relationship' => "translation",
			'relationship_guid' => $this->getGUID()
        	));
		foreach($entities as $entity){
			if($entity->language == $language){
				return $entity;
			}else{
				return false;
			}
		}
	}

	/** 
	 * Delete a translation
	 * @param string $language
	 * @return Entity|false Depending on success
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

	/**
	 * Look if has some translations
	 * @return bool
	 */
	public function hasTranslations(){
		$translations= FALSE;
		$relationships = get_entity_relationships($this->getGUID());
		
		foreach($relationships as $relation){
			if($relation->relationship == 'translation'){
				$translations = TRUE;
			}
		}
		if($translations){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * See in the relations if is a translation of other blog
	 * @return bool
	 */
	public function isTranslation(){
		$translation = FALSE;
		$relationships = get_entity_relationships($this->getGUID(), TRUE);
		foreach($relationships as $relation){
			if($relation->relationship == 'translation' && $relation->guid_two == $this->getGUID()){
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
