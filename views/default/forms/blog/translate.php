<?php
/**
 * Translate blog form
 *
 * @package Blog
 */

$draft_warning = $vars['draft_warning'];
if ($draft_warning) {
	$draft_warning = '<span class="message warning">' . $draft_warning . '</span>';
}

$action_buttons = '';
$delete_link = '';

if ($vars['guid']) {
	// add a delete button if editing
	$delete_url = "action/blog/delete?guid={$vars['guid']}";
	$delete_link = elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete elgg-state-disabled'
	));
}

$save_button = elgg_view('input/submit', array('value' => elgg_echo('save')));
$action_buttons = $save_button . $delete_link;

$title_label_old = elgg_echo('title');
$title_input_old = elgg_view('input/text', array(
	'name' => 'title',
	'id' => 'blog_title',
	'value' => $vars['title']
));

$title_label = "Translate title";
$title_input = elgg_view('input/text',array(
	'name' => 'title',
	'id' => 'blog_title'
));

$excerpt_label_old = elgg_echo('blog:excerpt');
$excerpt_input_old = elgg_view('input/text', array(
	'name' => 'excerpt',
	'id' => 'blog_excerpt',
	'value' => html_entity_decode($vars['excerpt'], ENT_COMPAT, 'UTF-8')
));

$excerpt_label = "Translate Excerpt";
$excerpt_input = elgg_view('input/text', array(
	'name' => 'excerpt',
	'id' => 'blog_excerpt',
));

$body_label_old = elgg_echo('blog:body');
$body_input_old = elgg_view('input/longtext', array(
	'name' => 'description',
	'id' => 'blog_description',
	'value' => $vars['description']
));

$body_label = "Translate Body";
$body_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'id' => 'blog_description',
));

$save_status = elgg_echo('blog:save_status');
if ($vars['guid']) {
	$entity = get_entity($vars['guid']);
	$saved = date('F j, Y @ H:i', $entity->time_created);
} else {
	$saved = elgg_echo('blog:never');
}

$status_label_old = elgg_echo('blog:status');
$status_input_old = elgg_view('input/dropdown', array(
	'name' => 'status',
	'id' => 'blog_status',
	'value' => $vars['status'],
	'options_values' => array(
		'draft' => elgg_echo('blog:status:draft'),
		'published' => elgg_echo('blog:status:published')
	)
));

$status_label = "Translated status";
$status_input = elgg_view('input/dropdown', array(
	'name' => 'status',
	'id' => 'blog_status',
	'options_values' => array(
		'draft' => elgg_echo('blog:status:draft'),
		'published' => elgg_echo('blog:status:published')
	)
));

$comments_label = elgg_echo('comments');
$comments_input = elgg_view('input/dropdown', array(
	'name' => 'comments_on',
	'id' => 'blog_comments_on',
	'value' => $vars['comments_on'],
	'options_values' => array('On' => elgg_echo('on'), 'Off' => elgg_echo('off'))
));

$tags_label = elgg_echo('tags');
$tags_input = elgg_view('input/tags', array(
	'name' => 'tags',
	'id' => 'blog_tags',
	'value' => $vars['tags']
));

$tags_label2 = elgg_echo('tags');
$tags_input2 = elgg_view('input/tags', array(
        'name' => 'tags',
        'id' => 'blog_tags',
));

$access_label_old = elgg_echo('access');
$access_input_old = elgg_view('input/access', array(
	'name' => 'access_id',
	'id' => 'blog_access_id',
	'value' => $vars['access_id']
));

$access_label = "Translated Access";
$access_input = elgg_view('input/access', array(
	'name' => 'access_id',
	'id' => 'blog_access_id',
));

//THIS HAS TO BE MOVED FROM HERE: Create a view for that.
//If is new it has to show user->language otherwise it has to show the old value
$la= array();
foreach (ElggTranslation::$languages as $lang){
        $la[$lang] = elgg_echo($lang);
}

$language_label = elgg_echo('language');
$language_input = elgg_view('input/dropdown', array(
        'name' => 'language',
        'id' => 'blog_status',
        'value' => $vars['language'],
	'options_values' => $la	
));

//Show the language of the user bydefault
$user = elgg_get_logged_in_user_entity();
$language_label2 = elgg_echo('language');
$language_input2 = elgg_view('input/dropdown', array(
        'name' => 'language',
        'id' => 'blog_status',
        'value' => $user->language,
        'options_values' => $la 
));

$categories_input = elgg_view('categories', $vars);

// hidden inputs
$container_guid_input = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => elgg_get_page_owner_guid()));
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['guid']));


echo <<<___HTML

$draft_warning
<div class="elgg-col-1of2">
<div>
	<label for="blog_title">$title_label_old</label>
	$title_input_old
</div>
<div>
	<label for="blog_excerpt">$excerpt_label_old</label>
	$excerpt_input_old
</div>

<label for="blog_description">$body_label_old</label>
        $body_input_old
        <br />


<div>
        <label for="blog_language">$language_label</label>
        $language_input
        <br />
</div>
<div>
        <label for="blog_tags">$tags_label</label>
        $tags_input
</div>

<div>
        <label for="blog_comments_on">$comments_label</label>
        $comments_input
</div>

<div>
        <label for="blog_access_id">$access_label_old</label>
        $access_input_old
</div>
<div>
	<label for="blog_status">$status_label_old</label>
	$status_input_old
</div>
</div>

<div class="elgg-col-2of2">
<div>
	<label for="blog_title">$title_label</label>
	$title_input
</div>
<div>
	<label for="blog_excerpt">$excerpt_label</label>
	$excerpt_input
</div>
	<label for="blog_description">$body_label</label>
	$body_input
	<br />

<div>
	<label for="blog_language">$language_label2</label>
	$language_input2
	<br />
</div>

<div>
        <label for="blog_tags2">$tags_label2</label>
        $tags_input2
</div>


<div>
	<label for="blog_access_id2">$access_label</label>
	$access_input
</div>

<div>
	<label for="blog_status2">$status_label</label>
	$status_input
	
</div>
</div>
$categories_input

<div class="elgg-subtext pvm mbn elgg-divide-top">
	$save_status <span class="blog-save-status-time">$saved</span>
</div>

$guid_input
$container_guid_input

$action_buttons

___HTML;
