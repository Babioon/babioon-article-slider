<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
	<div class="span6">
		<?php require ('_slider.php'); ?>
	</div>
	<div class="span6">
		<?php if ($rc > 0) :?>
			<ul class="sliderlinklist">
			<?php foreach ($list as $item) : ?>
				<li>
					<a href="<?php echo $item->readMore; ?>">
						<?php echo $item->title; ?>
					</a>
				</li>
			<?php endforeach;?>
			</ul>
		<?php endif; ?>
	</div>
</div>
