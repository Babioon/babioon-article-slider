<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');

abstract class modBabioonArticlesSlideshowHelperComK2
{
	public static function getList(&$params)
	{

		jimport('joomla.filesystem.file');
		$mainframe 		 = JFactory::getApplication();
		$componentParams = JComponentHelper::getParams('com_k2');
		$user 			 = JFactory::getUser();
		$db 			 = JFactory::getDBO();

		$jnow 		= JFactory::getDate();
		$now   		=  $jnow->toSql();
		$nullDate 	= $db->getNullDate();

		$k2_categories = $params->get('k2_categories', '');
		$k2_tags 	   = $params->get('k2_tags', '');
		$k2_article_id = $params->get('k2_article_id', '');
		$k2_count  	   = $params->get('k2_count', 5);
		$k2_ordering   = $params->get('k2_ordering', 'a.publish_up');
		$k2_featured   = $params->get('k2_featured', 2);

		$limitstart = JRequest::getInt('limitstart');

		$filterCategories = '';
		$filterTags       = '';
		$filterIds        = '';

		$query = "SELECT i.*, CASE WHEN i.modified = 0 THEN i.created ELSE i.modified END as lastChanged, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams";
		$query .= " FROM #__k2_items as i RIGHT JOIN #__k2_categories c ON c.id = i.catid";
		if (!empty($k2_tags))
		{
			$query .= ", #__k2_tags as t, #__k2_tags_xref as r";
		}
		$query .= " WHERE i.published = 1 AND i.access IN(".implode(',', $user->getAuthorisedViewLevels()).") AND i.trash = 0 AND c.published = 1 AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")  AND c.trash = 0";
		$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
		$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";

		if (!empty($k2_categories))
		{
			if (is_array($k2_categories))
			{
				JArrayHelper::toInteger($k2_categories);
				$filterCategories .= "i.catid IN(".implode(',', $k2_categories).")";
			}
			else
			{
				$filterCategories .= "i.catid=".(int)$k2_categories;
			}
		}

		if (!empty($k2_tags))
		{
			$query .= " AND t.id = r.tagID AND r.itemID = i.id";
			if (is_array($k2_tags))
			{
				foreach ($k2_tags as &$value)
				{
					$value = $db->Quote($value);
				}
				$filterTags .= "t.id IN(".implode(',', $k2_tags).")";
			}
			else
			{
				$filterTags .= "t.id=".$db->Quote($k2_tags);
			}
		}

		if (!empty($k2_article_id))
		{
			if (strpos($k2_article_id, ',') !== false)
			{
				$filterIds = "i.id IN ($k2_article_id)";
			}
			else
			{
				$filterIds = "i.id = ". (int) $k2_article_id;
			}
		}

		$query .= $filterCategories != "" ?  " AND $filterCategories" : "";
		$query .= $filterTags       != "" ?  " AND $filterTags" : "";
		$query .= $filterIds        != "" ?  " AND $filterIds" : "";

		switch ($k2_featured)
		{
			case 0:
				// No
				$query .= " AND i.featured = 0";
				break;

			case 1:
				// Yes
				break;

			default:
			case 2:
				// Only
				$query .= " AND i.featured = 1";
				break;

		}

		if ($mainframe->getLanguageFilter())
		{
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
		}

		switch ($k2_ordering)
		{

			case 'created' :
				$orderby = 'i.created ASC';
				break;

			case 'order' :
				if ($k2_featured == '2')
					$orderby = 'i.featured_ordering';
				else
					$orderby = 'i.ordering';
				break;

			case 'rand' :
				$orderby = 'RAND()';
				break;

			default :
			case 'publish_up' :
				$orderby = 'i.publish_up DESC';
				break;
		}

		$query .= " ORDER BY ".$orderby;
		$db->setQuery($query, 0, $k2_count);
		$items = $db->loadObjectList();
		$model = K2Model::getInstance('Item', 'K2Model');

		if (count($items))
		{
			foreach ($items as $item)
			{
			    $item->event = new stdClass;

				//Clean title
				$item->title = JFilterOutput::ampReplace($item->title);

				$date = JFactory::getDate($item->modified);
				$timestamp = '?t='.$date->toUnix();

				if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_XS.jpg'))
				{
					$item->imageXSmall = JURI::base(true).'/media/k2/items/cache/'.md5("Image".$item->id).'_XS.jpg';
					if ($componentParams->get('imageTimestamp'))
					{
						$item->imageXSmall .= $timestamp;
					}
				}

				if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_S.jpg'))
				{
					$item->imageSmall = JURI::base(true).'/media/k2/items/cache/'.md5("Image".$item->id).'_S.jpg';
					if ($componentParams->get('imageTimestamp'))
					{
						$item->imageSmall .= $timestamp;
					}
				}

				if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_M.jpg'))
				{
					$item->imageMedium = JURI::base(true).'/media/k2/items/cache/'.md5("Image".$item->id).'_M.jpg';
					if ($componentParams->get('imageTimestamp'))
					{
						$item->imageMedium .= $timestamp;
					}
				}

				if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_L.jpg'))
				{
					$item->imageLarge = JURI::base(true).'/media/k2/items/cache/'.md5("Image".$item->id).'_L.jpg';
					if ($componentParams->get('imageTimestamp'))
					{
						$item->imageLarge .= $timestamp;
					}
				}

				if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_XL.jpg'))
				{
					$item->imageXLarge = JURI::base(true).'/media/k2/items/cache/'.md5("Image".$item->id).'_XL.jpg';
					if ($componentParams->get('imageTimestamp'))
					{
						$item->imageXLarge .= $timestamp;
					}
				}

				if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_Generic.jpg'))
				{
					$item->imageGeneric = JURI::base(true).'/media/k2/items/cache/'.md5("Image".$item->id).'_Generic.jpg';
					if ($componentParams->get('imageTimestamp'))
					{
						$item->imageGeneric .= $timestamp;
					}
				}
				$item->imageSrc = '';
				$k2_picture = $params->get('k2_picture','');

				if ($k2_picture == '')
				{
					if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'src'.DS.md5("Image".$item->id).'.jpg'))
					{
						$item->imageSrc = JURI::base(true).'/media/k2/items/src/'.md5("Image".$item->id).'.jpg';
					}
				}
				else
				{
					if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).$k2_picture.'.jpg'))
					{
						$item->imageSrc = JURI::base(true).'/media/k2/items/cache/'.md5("Image".$item->id).$k2_picture.'.jpg';
					}
				}

				$image = 'image'.$params->get('itemImgSize', 'Small');
				if (isset($item->$image))
					$item->image = $item->$image;

				//Read more link
				$item->link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->catid.':'.urlencode($item->categoryalias))));

				if ($item->imageSrc != '')
				{
					$rows[] = $item;
				}
			}

			return $rows;
		}
	}
}
