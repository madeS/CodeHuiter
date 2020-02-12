<?php

namespace CodeHuiter\Facilities\Module\Media\SearchList;

use CodeHuiter\Modifier\IntModifier;
use CodeHuiter\Facilities\Module\Auth\AuthService;
use CodeHuiter\Facilities\Module\Auth\SearchList\UserSearcher;
use CodeHuiter\Facilities\Module\Auth\UserService;
use CodeHuiter\Facilities\Module\Media\Model\MediaModel;
use CodeHuiter\Facilities\SearchList\MultiTableSearcher\MultiTableSearcher;
use CodeHuiter\Facilities\SearchList\SearchListResult;

/**
 * This is legacy code. Dont use this code class, if it is not legacy
 */
class MediaSearcher extends MultiTableSearcher
{
    public $types = [
        'photo',
        'video',
        'zip',
    ];

    public function search(
        array $options = [],
        array $filters = [],
        array $pages = [],
        bool $requireResultCount = false
    ): SearchListResult {

        $model = new MediaModel();
        $table = $model->getModelTable();

        $this->sqls_table = $table;
        $this->sqls_extend = [];
        $this->sqls_connect  = ['user'];
        $this->sqls_select = " $table.* ";
        $this->sqls_from = $table;
        $this->sqls_where = " $table.active = 1 ";
        $this->sqls_order = " ORDER BY $table.id ASC ";

        if (isset($options['any'])){
            $this->sqls_where = ' 1 ';
        }

        if (in_array('user', $this->sqls_connect, true)){
            $this->sqls_select .= UserSearcher::SQL_SELECT_ADDIT_USER;
            $this->sqls_from .= " LEFT JOIN users ON $table.user_id = users.id ";
        }

        if ($filters) {
            if ($filters['query'] ?? '') {
                $this->sqls_where .= " AND (
						$table.id LIKE :{$table}_like_id
						OR $table.title LIKE :{$table}_like_title
						OR $table.description LIKE :{$table}_like_description
					) ";
                $this->bindings[":{$this->sqls_table}_like_id"] = "%{$filters['query']}%";
                $this->bindings[":{$this->sqls_table}_like_title"] = "%{$filters['query']}%";
                $this->bindings[":{$this->sqls_table}_like_description"] = "%{$filters['query']}%";
            }
            if (($filters['show'] ?? '') && $filters['show'] === 'special' && $this->getUserService()->isModerator($this->getAuthService()->user)) {
                $options['type'] = 'special';
            }
        }

        if (($options['type'] ?? '') && in_array($options['type'], $this->types, true)){
            $this->sqls_where .= " AND $table.type = :{$table}_type";
            $this->bindings[":{$table}_type"] = $options['type'];
        }
        if (isset($options['object_type'])){
            $this->sqls_where .= " AND $table.object_type = :{$table}_object_type";
            $this->bindings[":{$table}_object_type"] = $options['object_type'];
            if (isset($options['object_id'])){
                $this->sqls_where .= " AND $table.object_id = :{$table}_object_id";
                $this->bindings[":{$table}_object_id"] = IntModifier::normalizeBetween((int)$options['object_id'], 0);
            }
        }
        if (isset($options['user_id'])){
            $this->sqls_where .= " AND $table.user_id = :{$table}_user_id";
            $this->bindings[":{$table}_user_id"] = IntModifier::normalizeBetween((int)$options['user_id'], 0);
        }
        if (isset($options['video_code'])){
            $this->sqls_where .= " AND $table.video_code = :{$table}_video_code";
            $this->bindings[":{$table}_video_code"] = $options['video_code'];
        }
        if (isset($options['video_source'])){ // youtube vk vimeo
            $this->sqls_where .= " AND $table.video_source = :{$table}_video_source";
            $this->bindings[":{$table}_video_source"] = $options['video_source'];
        }
        if (isset($options['picture_orig'])){
            $this->sqls_where .= " AND $table.picture_orig = :{$table}_picture_orig";
            $this->bindings[":{$table}_picture_orig"] = $options['picture_orig'];
        }
        if (isset($options['order'])){
            if ($options['order'] === 'sortnum') {
                $options['order'] = 'sortnum ASC, id ASC';
            }
            $allowed_order = ['id DESC', 'id ASC', 'sortnum ASC, id ASC'];

            if (in_array($options['order'], $allowed_order, true)){
                $this->sqls_order = " ORDER BY $table.{$options['order']} ";
            }
        }

        return $this->searchFinish(MediaModel::class, $options, $filters, $pages, $requireResultCount);
    }

    private function getUserService(): UserService
    {
        return $this->application->get(UserService::class);
    }

    private function getAuthService(): AuthService
    {
        return $this->application->get(AuthService::class);
    }
}
