<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$active = ' active';
$rc     = count($list);

$moduleheadertag  = $params->get('header_tag','h3');
$captionheadertag = 'h' . (string)(((int) substr($moduleheadertag, 1)) + 1);

$linkimage = trim($item->readMore) != '' && $params->get('linkimage',0) == 1;
$linktitle = (trim($item->readMore) != '') && ($params->get('linktitle',1) == 1);
$linktitle = true;
if ($params->get('inherent_span',0) == 0)
{
	$moduleclass_sfx = str_replace(array('span1','span2','span3','span4','span5','span6','span7','span8','span9','span10','span11','span12'), '', $moduleclass_sfx);
}
?>


<div class="slider<?php echo $moduleclass_sfx; ?>">
	<?php if (!empty($list)) : ?>
		<div id="<?php echo $mycarouselId; ?>" class="carousel slide">

			<?php if ($params->get('indicator',0) == 1 && $rc != 0) : ?>
				<ol class="carousel-indicators">
				<?php for ($i=0; $i < $rc; $i++)  : ?>
						<li data-target="#<?php echo $mycarouselId; ?>" data-slide-to="<?php echo $i;?>"<?php echo $active != ' class="active"' ? "" : '';$active='';?>></li>
			  	<?php endfor; ?>
			  	</ol>
			<?php endif; ?>

		    <div class="carousel-inner">
		    	<?php foreach ($list as $item) : ?>
			    	<div class="item<?php echo $active;$active='';?>">
						<?php if ($linkimage)  : ?>
							<a href="<?php echo $item->readMore; ?>">
						<?php endif; ?>
			    		<img src="<?php echo $item->imageSrc;?>" border="0" alt="<?php echo $item->imageAlt;?>" />
			    		<?php if ($linkimage)  : ?>
			    			</a>
			    		<?php endif; ?>
			    		<?php if (trim($item->title) != '' || trim($item->imageCaption) != '') : ?>
			    			<div class="carousel-caption">
			    				<?php if (trim($item->title) != '') : ?>
			    					<<?php echo $captionheadertag; ?>>
			    						<?php if ($linktitle)  : ?>
			    							<a href="<?php echo $item->readMore; ?>">
			    						<?php endif; ?>
			    						<?php echo $item->title; ?>
			    						<?php if ($linktitle) : ?>
			    							</a>
			    						<?php endif; ?>
			    					</<?php echo $captionheadertag; ?>>
			    				<?php endif; ?>
				                <?php if (trim($item->imageCaption!= '')) : ?>
				                	<p>
				                		<?php echo $item->imageCaption;?>
				                	</p>
				                <?php endif; ?>
				            </div>
				        <?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ($params->get('control',1) == 1) : ?>
			    <?php if ($rc > 1) :?>
			    	<a class="left carousel-control" href="#<?php echo $mycarouselId; ?>" data-slide="prev">‹</a> <a class="right carousel-control" href="#<?php echo $mycarouselId; ?>" data-slide="next">›</a>
			    <?php endif; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>


<?php if ($params->get('autostart','1') == '1') : ?>


<script>
!function ($) {

  $(function(){
 // carousel demo
    $('#<?php echo $mycarouselId; ?>').carousel();
   });
}(window.jQuery)
</script>

<?php endif; ?>