<?php
/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

class NewsletterProMailChimp extends NewsletterProMailChimpApi
{
	public function ping()
	{
		$response = $this->call('helper/ping');

		if ($response)
			return $response['msg'];
		return false;
	}

	public function getLists($params = array())
	{
		$response = $this->call('lists/list', $params);
		if ($response)
			return $response['data'];
		return false;
	}

	public function getListById($id)
	{
		$response = $this->call('lists/list', array(
			'filters' => array(
				'list_id' => $id,
			)
		));

		if ($this->hasErrors())
			return false;

		return $response['data'][0];
	}

	public function getListMembers($id_list, $start = 0, $limit = 100, $params = array())
	{
		$params['id'] = $id_list;

		$params['opts'] = array(
			'start' => $start,
			'limit' => $limit,
		);

		$response = $this->call('lists/members', $params);

		if (!$response)
			return false;

		return $response['data'];
	}

	private function buildSubscribeUsers($users)
	{
		$array = array();

		foreach ($users as $user)
		{
			$array[] = array(
				'email' => array(
					'email' => $user['EMAIL'],
				),
				'merge_vars' => $user,
			);
		}
		return $array;
	}

	public function subscribe($id, $users = array(), $params = array())
	{
		$params['id'] = $id;

		if ($users)
			$params['batch'] = $this->buildSubscribeUsers($users);

		// default data
		$params['double_optin']      = false;
		$params['update_existing']   = true;
		$params['replace_interests'] = true;

		return $this->call('lists/batch-subscribe', $params);
	}

	private function buildUnsubscribeUsers($users)
	{
		$array = array();

		foreach ($users as $user)
		{
			$array[] = array(
				'email' => $user['email'],
			);
		}

		return $array;
	}

	public function unsubscribe($id, $users = array(), $params = array())
	{
		$params['id'] = $id;

		if ($users)
			$params['batch'] = $this->buildUnsubscribeUsers($users);

		// default options
		$params['delete_member'] = false;
		$params['send_goodbye']  = false;
		$params['send_notify']   = false;

		return $this->call('lists/batch-unsubscribe', $params);
	}

	public function deleteUsers($id, $users = array(), $params = array())
	{
		$params['delete_member'] = true;
		return $this->unsubscribe($id, $users, $params);
	}

	public function validateTag($tag)
	{
		if (!preg_match('/^[A-Z0-9_]+$/', $tag))
			$this->addError('Invalid tag name "'.$tag.'" for creation. The tag need to have the format [A-Z0-9_].');
		else if (Tools::strlen($tag) > 10)
			$this->addError('Invalid tag length for tag "'.$tag.'".');

		if ($this->hasErrors())
			return false;
		return true;
	}

	public function listAddVar($id, $tag, $name, $params = array())
	{
		Tools::strtoupper($tag);

		$params['id']   = $id;
		$params['tag']  = $tag;
		$params['name'] = $name;

		if (!$this->validateTag($tag))
			return false;

		return $this->call('lists/merge-var-add', $params);
	}

	public function listUpdateVar($id, $tag, $name, $params = array())
	{
		Tools::strtoupper($tag);

		$params['id']  = $id;
		$params['tag'] = $tag;
		$params['options']['name'] = $name;

		if (!$this->validateTag($tag))
			return false;

		return $this->call('lists/merge-var-update', $params);
	}

	public function listSetVar($id, $tag, $value, $params = array())
	{
		Tools::strtoupper($tag);

		$params['id'] = $id;
		$params['tag'] = $tag;
		$params['value'] = $value;

		if (!$this->validateTag($tag))
			return false;

		return $this->call('lists/merge-var-set', $params);
	}

	public function listResetVar($id, $tag, $params = array())
	{
		Tools::strtoupper($tag);

		$params['id'] = $id;
		$params['tag'] = $tag;

		if (!$this->validateTag($tag))
			return false;

		return $this->call('lists/merge-var-reset', $params);
	}

	public function listDeleteVar($id, $tag, $params = array())
	{
		$params['id'] = $id;
		$params['tag'] = $tag;

		return $this->call('lists/merge-var-del', $params);
	}

	public function listDeleteVars($id)
	{
		$errors    = array();
		$vars      = $this->listGetVars($id);
		$tags_name = self::grep($vars['merge_vars'], 'tag');
		$search    = array_search('EMAIL', $tags_name);

		if ($search !== false)
			unset($tags_name[$search]);

		foreach ($tags_name as $tag)
		{
			$res = $this->listDeleteVar($id, $tag);
			if (!$res)
				$this->mergeErrors($errors);
		}

		if (empty($errors))
			return true;
		return false;
	}

	public function listGetVars($id, $params = array())
	{
		$params['id'] = array($id);

		$response = $this->call('lists/merge-vars', $params);

		if ($this->hasErrors())
			return false;

		return $response['data'][0];
	}

	public function listAddGroup($id, $name, $grouping_id = null, $params = array())
	{
		$params['id'] = $id;
		$params['group_name'] = $name;

		if (isset($grouping_id))
			$params['grouping_id'] = $grouping_id;

		return $this->call('lists/interest-group-add', $params);
	}

	public function listUpdateGroup($id, $old_name, $new_name, $grouping_id = null, $params = array())
	{
		$params['id'] = $id;
		$params['old_name'] = $old_name;
		$params['new_name'] = $new_name;

		if (isset($grouping_id))
			$params['grouping_id'] = $grouping_id;

		return $this->call('lists/interest-group-update', $params);
	}

	public function listDeleteGroup($id_list, $name, $grouping_id = null, $params = array())
	{
		$params['id'] = $id_list;
		$params['group_name'] = $name;

		if (isset($grouping_id))
			$params['grouping_id'] = $grouping_id;

		return $this->call('lists/interest-group-del', $params);
	}

	public function listAddGrouping($id, $name, $type = 'checkboxes', $groups = array(), $params = array())
	{
		$params['id'] = $id;
		$params['name'] = $name;
		$params['type'] = $type;
		$params['groups'] = $groups;

		$response = $this->call('lists/interest-grouping-add', $params);

		if ($this->hasErrors())
			return false;

		return $response['id'];
	}

	public function listDeleteGrouping($grouping_id, $params = array())
	{
		$params['grouping_id'] = $grouping_id;
		$response = $this->call('lists/interest-grouping-del', $params);

		if ($this->hasErrors())
			return false;
		return $response['complete'];
	}

	public function listUpdateGroupingName($grouping_id, $value, $params = array())
	{
		$params['grouping_id'] = $grouping_id;
		$params['name'] = 'name';
		$params['value'] = $value;

		$this->call('lists/interest-grouping-update', $params);
		if ($this->hasErrors())
			return false;
		return true;
	}

	public function listGetGroupings($id_list, $params = array())
	{
		$params['id'] = $id_list;

		return $this->call('lists/interest-groupings', $params);
	}

	public function listUpdateMember($id_list, $user = array(), $params = array())
	{
		$params['id'] = $id_list;
		$params['email'] = array(
			'email' => $user['EMAIL'],
		);

		$params['merge_vars'] = $user;
		$params['replace_interests'] = true;

		return $this->call('lists/update-member', $params);
	}

	public function getAccountDetails($params = array())
	{
		return $this->call('helper/account-details', $params);
	}

	public function getTemplates($types = null, $filters = null, $params = array())
	{
		if (isset($types))
			$params['types'] = $types;
		else
		{
			$params['types']['user'] = true;
			$params['types']['gallery'] = true;
			$params['types']['base'] = true;
		}
		if (isset($filters))
			$params['filters'] = $filters;

		return $this->call('templates/list', $params);
	}

	public function getTemplateContent($template_id, $type = null, $params = array())
	{
		$params['template_id'] = $template_id;

		if (isset($type))
			$params['type'] = $type;

		return $this->call('templates/info', $params);
	}

	public function templateAdd($name, $html, $folder_id = null, $params = array())
	{
		$params['name'] = $name;
		$params['html'] = $html;

		if (isset($folder_id))
			$params['folder_id'] = $folder_id;

		return $this->call('templates/add', $params);
	}

	public function templateUpdate($template_id, $name, $html, $folder_id = null, $params = array())
	{
		$params['template_id']  = $template_id;

		$params['values']['name'] = $name;
		$params['values']['html'] = $html;

		if (isset($folder_id))
			$params['values']['folder_id'] = $folder_id;

		return $this->call('templates/update', $params);
	}

	public function templateUndel($template_id, $params = array())
	{
		$params['template_id']  = $template_id;

		return $this->call('templates/undel', $params);
	}

	public function orderAdd($order, $params = array())
	{
		$params['order'] = $order;
		return $this->call('ecomm/order-add', $params);
	}

	public function orderDelete($store_id, $order_id, $params = array())
	{
		$params['store_id'] = $store_id;
		$params['order_id'] = $order_id;

		return $this->call('ecomm/order-del', $params);
	}

	public function getOrders($cid = null, $start = null, $limit = null, $since = null, $params = array())
	{
		if (isset($cid))
			$params['cid'] = (string)$cid;
		
		if (isset($start))
			$params['start'] = (int)$start;
		
		if (isset($limit))
			$params['limit'] = (int)$limit;
		
		if (isset($since))
			$params['since'] = (string)$since;

		return $this->call('ecomm/orders', $params);
	}
}
?>