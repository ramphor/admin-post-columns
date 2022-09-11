<?php
namespace Ramphor\PostColumns;

use Ramphor\PostColumns\Interfaces\ColumnInterface;

class ColumnsManager
{
    /**
     * @var self[]
     */
    protected static $instances = [];

    /**
     *
     * @var string
     */
    protected $currentPostType;

    /**
     * @var \Ramphor\PostColumns\Column[]
     */
    protected $columns = [];

    protected $sortableColumns = [];

    protected function __construct($postType = 'post')
    {
        $this->setCurrentPostType($postType);
        $this->initHooks();
    }

    protected function setCurrentPostType($postType)
    {
        if (post_type_exists($postType)) {
            $this->currentPostType = $postType;
        }
    }

    /**
     * @return self
     */
    public static function create($postType = 'post')
    {
        if (!isset(static::$instances[$postType])) {
            static::$instances[$postType] = new static($postType);
        }
        return static::$instances[$postType];
    }

    public static function getInstance($postType)
    {
    }

    /**
     * @param string $postType The post type
     *
     * @return  self|null
     */
    public function changePostType($postType)
    {
        if (isset(static::$instances[$postType])) {
            return static::$instances[$postType];
        }
    }

    /**
     * @param \Ramphor\PostColumns\Column $column
     *
     * @return self
     */
    public function addColumn($column)
    {
        if ($column instanceof ColumnInterface && !empty($column->getId())) {
            $this->columns[$column->getId()] = $column;

            // Use for customize CSS
            $column->setAppliedPostType($this->currentPostType);
        }
        return $this;
    }

    public function getColumnById($columnId)
    {
        if (isset($this->columns[$columnId])) {
            return $this->columns[$columnId];
        }
    }

    protected function initHooks()
    {
        if ($this->currentPostType) {
            add_filter("manage_edit-{$this->currentPostType}_columns", [$this, 'registerColumns']);
            add_filter("manage_edit-{$this->currentPostType}_sortable_columns", [$this, 'registerSortableColumns']);
            if ($this->currentPostType ===  'post') {
                add_action('manage_posts_custom_column', [$this, 'renderColumns'], 10, 2);
            } else {
                add_action("manage_{$this->currentPostType}_posts_custom_column", [$this, 'renderColumns'], 10, 2);
            }
        }
    }

    public function registerColumns($columns)
    {
        if (empty($this->columns)) {
            return $columns;
        }

        foreach ($this->columns as $column) {
            $column->registerTo($columns);
        }

        return $columns;
    }

    public function registerSortableColumns($columns)
    {
        if (empty($this->sortableColumns)) {
            return $columns;
        }

        return $columns;
    }

    public function renderColumns($column, $postId)
    {
        $column = $this->getColumnById($column);
        if (!is_null($column)) {
            $column->setPostId($postId);
            $column->render();
        }
    }
}
