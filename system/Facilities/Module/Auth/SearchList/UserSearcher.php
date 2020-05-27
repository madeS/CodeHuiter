<?php

namespace CodeHuiter\Facilities\Module\Auth\SearchList;

use CodeHuiter\Facilities\Module\Auth\AuthService;
use CodeHuiter\Facilities\Module\Auth\Model\UserRepository;
use CodeHuiter\Facilities\SearchList\MultiTableSearcher\MultiTableSearcher;
use CodeHuiter\Facilities\SearchList\SearchListResult;
use CodeHuiter\Service\DateService;

/**
 * This is legacy code. Dont use this code class, if it is not legacy
 */
class UserSearcher extends MultiTableSearcher
{
    public const SQL_SELECT_ADDIT_USER = "
			, users.id as inn_user_id 
			, users.name as inn_user_name 
			, users.login as inn_user_login
			, users.firstname as inn_user_firstname
			, users.lastname as inn_user_lastname
			, users.picture_id as inn_user_picture_id
			, users.picture_preview as inn_user_picture_preview
		";

    public function search(
        array $options = [],
        array $filters = [],
        array $pages = [],
        bool $requireResultCount = false
    ): SearchListResult {
        $repository = $this->getUserRepository()->getRelationalRepository();

        $userTable = $repository->getConfig()->table;

        $this->sqls_table = $userTable;
        $this->sqls_extend = ['data_info'];
        $this->sqls_one = false;
        $this->sqls_connect  = array();
        $this->sqls_select = " $userTable.* ";
        $this->sqls_from = $userTable;
        $this->sqls_where = " 1 ";
        $this->sqls_order = " ORDER BY $userTable.lastact DESC ";

        if ($filters){
            if ($filters['query'] ?? '') {
                $this->sqls_where .= " AND (
						$userTable.name LIKE :{$this->sqls_table}_like_name
						OR $userTable.login LIKE :{$this->sqls_table}_like_login
						OR $userTable.firstname LIKE :{$this->sqls_table}_like_firstname
						OR $userTable.lastname LIKE :{$this->sqls_table}_like_lastname
						OR {$this->sqls_table}.alias = :{$this->sqls_table}_alias
					) ";
                $this->bindings[":{$this->sqls_table}_like_name"] = "%{$filters['query']}%";
                $this->bindings[":{$this->sqls_table}_like_login"] = "%{$filters['query']}%";
                $this->bindings[":{$this->sqls_table}_like_firstname"] = "%{$filters['query']}%";
                $this->bindings[":{$this->sqls_table}_like_lastname"] = "%{$filters['query']}%";
                $this->bindings[":{$this->sqls_table}_alias"] = $filters['query'];
            }
            if ($filters['show'] ?? '') {
                if ($filters['show'] === 'random'){
                    $this->sqls_order =  " ORDER BY RAND() ";
                    $requireResultCount = false;
                }
                if ($filters['show'] === 'last'){

                }
                if ($filters['show'] === 'online'){
                    $lastactborder = $this->getDateService()->getCurrentTimestamp() - $this->application->config->authConfig->onlineTime;
                    $this->sqls_where .= " AND users.lastact > $lastactborder ";
                }
                if ($filters['show'] === 'moderators'){
                    $this->sqls_where .= " AND users.groups LIKE :{$this->sqls_table}_like_groups";
                    $moderatorGroup = AuthService::GROUP_MODERATOR;
                    $this->bindings[":{$this->sqls_table}_like_groups"] = "%$moderatorGroup%";
                }
            }
//			if ($this->mm->g($filters['going_tour'])){
//				$this->sqls_from .= " LEFT JOIN user_geo ON user_geo.user_id = users.id AND user_geo.geo_type = 'tour_going' AND user_geo.object_id = {$this->mm->sqlInt($filters['going_tour'])} ";
//				$this->sqls_where .= " AND user_geo.object_id = '{$this->mm->sqlInt($filters['going_tour'],0)}' ";
//				$sqlWhereRequire = '';
//			}
        }

        return $this->searchFinish($repository, $options, $filters, $pages, $requireResultCount);
    }

    private function getUserRepository(): UserRepository
    {
        return $this->application->get(UserRepository::class);
    }

    private function getDateService(): DateService
    {
        return $this->application->get(DateService::class);
    }
}
