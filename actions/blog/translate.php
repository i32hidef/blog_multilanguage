<?php
/**
 * Translate blog entity
 *
 * @package Blog
 */

// start a new sticky form session in case of failure
elgg_make_sticky_form('blog');

// store errors to pass along
$error = FALSE;
$error_forward_url = REFERER;
$user = elgg_get_logged_in_user_entity();

// edit or create a new entity
$guid = get_input('guid');

if ($guid) {
	$entity = get_entity($guid);
	if (elgg_instanceof($entity, 'object', 'blog') && $entity->canEdit()) {
		$blog = $entity;
	} else {
		register_error(elgg_echo('blog:error:post_not_found'));
		forward(get_input('forward', REFERER));
	}

	// save some data for revisions once we save the new edit
	$revision_text = $blog->description;
	$new_post = $blog->new_post;
		
	//$language = get_input('language');
	//We save the translation entity	
	$blogTranslated = new ElggBlog();
	$blogTranslated->subtype = 'blog';
	$new_post2 = TRUE;
}

// set the previous status for the hooks to update the time_created and river entries
$old_status = $blog->status;

// set defaults and required values.
$values = array(
	'title' => '',
	'description' => '',
	'status' => 'draft',
	'access_id' => ACCESS_DEFAULT,
	'comments_on' => 'On',
	'excerpt' => '',
	'tags' => '',
	'language' => '',
	'container_guid' => (int)get_input('container_guid'),
);

// fail if a required entity isn't set
$required = array('title', 'description');

// load from POST and do sanity and access checking
foreach ($values as $name => $default) {
	$value = get_input($name, $default);

	if (in_array($name, $required) && empty($value)) {
		$error = elgg_echo("blog:error:missing:$name");
	}

	if ($error) {
		break;
	}

	switch ($name) {
		case 'tags':
			if ($value) {
				$values[$name] = string_to_tag_array($value);
			} else {
				unset ($values[$name]);
			}
			break;

		case 'excerpt':
			if ($value) {
				$value = elgg_get_excerpt($value);
			} else {
				$value = elgg_get_excerpt($values['description']);
			}
			$values[$name] = $value;
			break;
		
		/* Is not necesary*/
		case 'language':
			$blogTranslated->set($name, $value);
			error_log("LANGUAGE " . $blogTranslated->$name);
			$values[$name] = $value;
			break;
	
		case 'container_guid':
			// this can't be empty or saving the base entity fails
			if (!empty($value)) {
				if (can_write_to_container($user->getGUID(), $value)) {
					$values[$name] = $value;
				} else {
					$error = elgg_echo("blog:error:cannot_write_to_container");
				}
			} else {
				unset($values[$name]);
			}
			break;

		// don't try to set the guid
		case 'guid':
			unset($values['guid']);
			break;

		default:
			$values[$name] = $value;
			break;
	}
}

// assign values to the entity, stopping on error.
if (!$error) {
	foreach ($values as $name => $value) {
		if (FALSE === ($blogTranslated->$name = $value)) {
			$error = elgg_echo('blog:error:cannot_save' . "$name=$value");
			break;
		}
	}
}

// only try to save base entity if no errors
if (!$error) {
	if ($blogTranslated->save()) {
		$blog->addTranslation($blogTranslated->getGUID());

		// remove sticky form entries
		elgg_clear_sticky_form('blog');

		// remove autosave draft if exists
		$blogTranslated->clearAnnotations('blog_auto_save');

		// no longer a brand new post.
		$blogTranslated->clearMetadata('new_post');
		error_log("ACTION LANGUAGE " . $blogTranslated->language);
		// if this was an edit, create a revision annotation
		if (!$new_post && $revision_text) {
			$blogTranslated->annotate('blog_revision', $revision_text);
		}

		system_message(elgg_echo('blog:message:saved'));

		$status = $blogTranslated->status;
		$db_prefix = elgg_get_config('dbprefix');

		// add to river if changing status or published, regardless of new post
		// because we remove it for drafts.
		if (($new_post || $old_status == 'draft') && $status == 'published') {
			add_to_river('river/object/blog/create', 'create', elgg_get_logged_in_user_guid(), $blogTranslated->getGUID());

			$blogTranslated->time_created = time();
			$blogTranslated->save();
		} elseif ($old_status == 'published' && $status == 'draft') {
			$q = "DELETE FROM {$db_prefix}river
				WHERE object_guid = $blogTranslated->guid AND action_type = 'create'";
			delete_data($q);
		}

		if ($blogTranslated->status == 'published') {
			forward($blogTranslated->getURL());
		} else {
			forward("blog/edit/$blogTranslated->guid");
		}
	} else {
		register_error(elgg_echo('blog:error:cannot_save'));
		forward($error_forward_url);
	}
} else {
	register_error($error);
	forward($error_forward_url);
}
