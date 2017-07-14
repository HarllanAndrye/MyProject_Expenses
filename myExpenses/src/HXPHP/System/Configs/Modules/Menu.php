<?php

namespace HXPHP\System\Configs\Modules;

class Menu
{
	/**
	public $configs = [
		'container' => 'nav', // Tag container (opcional). Ex: nav
		'container_id' => '',
		'container_class' => 'navbar navbar-default',
		'menu_id' => 'menu',
		'menu_class' => 'navbar-nav nav',
		'menu_item_class' => 'menu-item',
		'menu_item_active_class' => 'active',
		'menu_item_dropdown_class' => 'dropdown',
		'link_before' => '<span>', // Conteúdo anterior ao título
		'link_after' => '</span>', // Conteúdo posterior ao título
		'link_class' => 'menu-link',
		'link_active_class' => 'menu-active-link',
		'link_dropdown_class' => 'dropdown-toggle',
		'link_dropdown_attrs' => [
			'data-toggle' => 'dropdown'	// Data Atributos para ativação do dropdown
		],
		'dropdown_class' => 'dropdown-menu',
		'dropdown_item_class' => 'dropdown-item',
		'dropdown_item_active_class' => 'active'
	];
	*/
	// @Harllan
	public $configs = [
		'container' => 'div',
		'container_id' => 'sidebar',
		'container_class' => 'nav-collapse',
		'menu_id' => 'nav-accordion',
		'menu_class' => 'sidebar-menu',
		'menu_item_class' => 'mt',
		'menu_item_active_class' => 'active',
		'link_before' => '<span>',
		'link_after' => '</span>',
		'link_class' => '',
		'link_active_class' => 'active',
		'submenu_class' => 'sub', // Para Submenu
		'submenu_item_class' => 'sub-menu',
		'submenu_item_active_class' => 'active'
	];

	public $itens = [];

	public function setConfigs(array $configs)
	{
		return $this->configs = array_merge($this->configs, $configs);
	}

	public function setMenus(array $itens, $role = 'default')
	{
		return $this->itens[$role] = $itens;
	}
}