<?php

namespace GrapheneICT\NovaPermissions;

use GrapheneICT\NovaPermissions\Nova\Permission;
use GrapheneICT\NovaPermissions\Nova\Role;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaPermissions extends Tool
{
    /**
     * @var mixed
     */
    public $permissionResource = Permission::class;

    /**
     * @var mixed
     */
    public $roleResource = Role::class;

    /**
     * showInNavigation
     *
     * @var bool
     */
    public $showInNavigation = true;

    /**
     * hideInNavigation function
     *
     * @return void
     */
    public function hideInNavigation()
    {
        $this->showInNavigation = false;
        return $this;
    }

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('NovaPermissions', __DIR__ . '/../dist/js/tool.js');
        Nova::style('NovaPermissions', __DIR__ . '/../dist/css/tool.css');

        Nova::resources([
            $this->roleResource,
            $this->permissionResource,
        ]);
    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Roles & Permissions';
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return $this->showInNavigation ? view('nova-permissions::navigation') : "";
    }

    /**
     * @param  string  $permissionResource
     * @return mixed
     */
    public function permissionResource(string $permissionResource)
    {
        $this->permissionResource = $permissionResource;

        return $this;
    }
    /**
     * @param  string  $roleResource
     * @return mixed
     */
    public function roleResource(string $roleResource)
    {
        $this->roleResource = $roleResource;

        return $this;
    }
}
