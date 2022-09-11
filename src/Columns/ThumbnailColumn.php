<?php
namespace Ramphor\PostColumns\Columns;

use Ramphor\PostColumns\Abstracts\ColumnAbstract;

class ThumbnailColumn extends ColumnAbstract
{
    protected $thumbnailSize = [60, 60];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        add_action('admin_head', [$this, 'customizeThumbnailColumnWidth']);
    }

    public function setThumbnailSize($size)
    {
        if (!empty($size)) {
            $this->thumbnailSize = $size;
        }
    }

    public function render()
    {
        $thumbnailId = get_post_thumbnail_id($this->getPostId());

        if ($thumbnailId > 0) {
            echo wp_get_attachment_image($thumbnailId, $this->thumbnailSize, true);
        }
    }

    public function customizeThumbnailColumnWidth()
    {
        $screen = get_current_screen();
        if (!($screen->base === 'edit' && (isset($screen->post_type) && $screen->post_type === $this->postType))) {
            return;
        }
        $thumbnailWidth = 0;
        if (is_array($this->thumbnailSize) && count($this->thumbnailSize)) {
            $thumbnailWidth = $this->thumbnailSize[0];
        }
        if ($thumbnailWidth < 80) {
            $thumbnailWidth = 80;
        }
        ?>
        <style>
            .manage-column.column-<?php echo $this->getId(); ?> {
                width: <?php echo $thumbnailWidth; ?>px;
            }
            .<?php echo $this->getId(); ?>.column-<?php echo $this->getId(); ?> {
                text-align: center;
            }
        </style>
        <?php
    }
}
