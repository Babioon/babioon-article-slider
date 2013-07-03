<?php
/**
 * babioon cat
 * @package    BABIOON_ARTICLE_SLIDSHOW
 * @author     Robert Deutz <rdeutz@gmail.com>
 * @copyright  2013 Robert Deutz Business Solution
 * @license    GNU General Public License version 2 or later
 **/

// No direct access
defined('_JEXEC') or die;

// Check what is the source for the slider and include the helper only once
$source      = $params->get('source');
$count_slide = 0;
$list        = array();

if ($source == 'com_k2')
{
	require_once dirname(__FILE__).'/helper_com_k2.php';
	$items = modBabioonArticlesSlideshowHelperComK2::getList($params);
	if (!empty($items))
	{
		foreach ($items as $i)
		{
			$obj = new stdClass;

			$obj->imageSrc     =  $i->imageSrc;
			$obj->imageAlt     =  $i->title;
			$obj->imageCaption =  '';
			$obj->title        =  $i->title;
			$obj->readMore     =  $i->link;

			$list[] = clone($obj);
			unset($obj);
			$count_slide++;
		}
	}
}
else
{
	require_once dirname(__FILE__).'/helper_com_content.php';
	$list = modBabioonArticlesSlideshowHelperComContent::getList($params);

	foreach ($items as $i)
	{
		$obj = new stdClass;

		$obj->imageSrc     =  $i->imageSrc;
		$obj->imageAlt     =  $i->title;
		$obj->imageCaption =  '';
		$obj->title        =  $i->title;
		$obj->readMore     =  $i->link;

		$list[] = clone($obj);
		unset($obj);
		$count_slide++;
	}

}

// Id handeling
$mycarouselId = 'mycarousel';

if ($params->get('autoid','1') == '1')
{
	$mycarouselId .= $module->id;
}
/*
if ($params->get('autostart','1') == '1')
{
	$doc = JFactory::getDocument();
	$script = "
	jQuery(document).ready(function($) {
	     $('#$mycarouselId').carousel();
	   });
	}
	";
	$script1 = "
	$(document).ready(
	     $('#$mycarouselId').carousel();
	   );
	}
	";
	$script1 = "
	    $('#$mycarouselId').carousel();
	";
	$doc->addScriptDeclaration($script);
}
*/
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_babioon_article_slideshow', $params->get('layout', 'default'));
