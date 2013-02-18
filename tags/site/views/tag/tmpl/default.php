<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/');
JHTML::_('script', 'tags.js', 'media/com_tags/js/');
$state = @$this->state;
$items = @$this->items;
?>

<div id="tag" class="tag default">

    <h3 id='tag_header' class="componentheading">
        <span><?php echo JText::_( "Tag" ); ?><?php echo (!empty($this->tag->tag_name)) ? ": ".$this->tag->tag_name : ""; ?></span>
    </h3>

    <div id="tags_searchresults">
        <div id="searchresults_results">
            <?php $i=0; $k=0; ?>
            <?php foreach (@$items as $item) : ?>
            <div class="tag_item">
                <a href="<?php echo JRoute::_( $item->link_view ); ?>">
                    <?php echo $item->item_name; ?>
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
            <div id="results_counter" class="pagination"><?php echo @$this->pagination->getResultsCounter(); ?></div>
            <?php echo @$this->pagination->getListFooter(); ?>
        </div>
    </div>
    
</div>