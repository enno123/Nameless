<?php

/**
 * No params
 *
 * @return string JSON Array
 */
class ListUsersEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'users';
        $this->_module = 'Core';
        $this->_description = 'List all users on the NamelessMC site';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api): void {
        $query = 'SELECT u.id, u.username, u.isbanned AS banned, u.active FROM nl2_users u';
        $where = [];
        $params = [];

        $operator = isset($_GET['operator']) && $_GET['operator'] == 'OR'
            ? ' OR '
            : ' AND ';

        if (isset($_GET['group_id'])) {
            $query .= ' INNER JOIN nl2_users_groups ug ON u.id = ug.user_id';
            $where[] = 'ug.group_id = ?';
            $params[] = $_GET['group_id'];
        }

        if (isset($_GET['integration'])) {
            $query .= ' INNER JOIN nl2_users_integrations ui ON ui.user_id=u.id INNER JOIN nl2_integrations i ON i.id=ui.integration_id';
            $where[] = 'i.name = ?';
            $params[] = $_GET['integration'];
        }

        if (isset($_GET['banned'])) {
            $where[] = '`u`.`isbanned` = ' . ($_GET['banned'] == 'true' ? '1' : '0');
        }

        if (isset($_GET['active'])) {
            $where[] = '`u`.`active` = ' . ($_GET['active'] == 'true' ? '1' : '0');
        }

        // Build where string
        $where_string = ' WHERE ';
        foreach ($where as $item) {
            $where_string .= $item . $operator;
        }
        $where_string = rtrim($where_string, $operator);

        $return_array = [
            'limit' => -1,
            'offset' => 0,
        ];

        if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
            $limit = (int) $_GET['limit'];
            if ($limit >= 1) {
                $return_array['limit'] = $limit;
                $where_string .= ' LIMIT ' . $limit;

                if (isset($_GET['offset']) && is_numeric($_GET['offset'])) {
                    $offset = (int) $_GET['offset'];
                    $return_array['offset'] = $offset;
                    $where_string .= ' OFFSET ' . $offset;
                }
            }
        }

        $users = $api->getDb()->selectQuery($query . $where_string, $params)->results();

        $users_json = [];
        foreach ($users as $user) {
            $integrations = [];
            $integrations_query = $api->getDb()->selectQuery('SELECT ui.*, i.name FROM nl2_users_integrations ui INNER JOIN nl2_integrations i ON i.id=ui.integration_id WHERE user_id = ? AND username IS NOT NULL AND identifier IS NOT NULL', [$user->id])->results();
            foreach ($integrations_query as $integration) {
                $integrations[] = [
                    'integration' => Output::getClean($integration->name),
                    'identifier' => Output::getClean($integration->identifier),
                    'username' => Output::getClean($integration->username),
                    'verified' => (bool) $integration->verified,
                    'linked_date' => $integration->date,
                    'show_publicly' => (bool) $integration->show_publicly,
                ];
            }

            $user_json = [
                'id' => (int)$user->id,
                'username' => $user->username,
                'banned' => (bool)$user->banned,
                'verified' => (bool)$user->active,
                'integrations' => $integrations
            ];

            $users_json[] = $user_json;
        }

        $return_array['users'] = $users_json;

        $api->returnArray($return_array);
    }
}
