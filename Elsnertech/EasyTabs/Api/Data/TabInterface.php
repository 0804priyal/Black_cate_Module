<?php

namespace Elsnertech\EasyTabs\Api\Data;

interface TabInterface {

	const ENTITY_ID = 'entity_id';
	const TITLE = 'title';
	const TAB_CLASS = 'class';
	const CONTENT = 'content';
	const IS_ACTIVE = 'is_active';
	const TAB_SORT = 'tab_sort';

	public function getEntityId();

	public function setEntityId($entityId);

	public function getTitle();

	public function setTitle($title);

	public function getClass();

	public function setClass($class);

	public function getContent();

	public function setContent($content);

	public function getIsActive();

	public function setIsActive($isActive);

	public function getTabSort();

	public function setTabSort($tabSort);
}
