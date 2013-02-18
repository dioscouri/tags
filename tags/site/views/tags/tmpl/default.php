<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/');
JHTML::_('script', 'tags.js', 'media/com_tags/js/');
$state = @$this->state;
$items = @$this->scope_tags;
?>

<div id="tags" class="tags default">

	<h3 id='tags_category_header' class="componentheading">
        <span><?php echo JText::_( "Tags" ); ?></span>
    </h3>

    <div id="tags_searchresults">
        <div id="searchresults_results">
            <?php $i=0; $k=0; ?>
            <?php foreach (@$items as $item) : ?>
            <div class="tag_item">
                <a href="<?php echo JRoute::_( "index.php?option=com_tags&view=tag&id=" . $item->tag_id ); ?>">
                    <?php echo $item->tag_name; ?>
                </a>
            </div>
            
            <div class="reset"></div>
            
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>

            <?php if (!count(@$items)) : ?>
            <div class="tag_item">
                <?php echo JText::_('No items found'); ?>
            </div>
            <?php endif; ?>
        </div>
       
        <div id="searchresults_footer">
            <form action="<?php echo JRoute::_( @$form['action']."&limitstart=".@$state->limitstart )?>" method="post" name="adminForm" enctype="multipart/form-data">
            <div id="results_counter" class="pagination"><?php echo @$this->pagination->getResultsCounter(); ?></div>
            <?php echo @$this->pagination->getListFooter(); ?>
            <?php echo $this->form['validate']; ?>
            </form>
        </div>
    </div>
    
</div>