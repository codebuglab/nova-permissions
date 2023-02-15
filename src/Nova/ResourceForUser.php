<?php

namespace GrapheneICT\NovaPermissions\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Resource as NovaResource;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Scout\Builder;
use Illuminate\Support\Str;

abstract class ResourceForUser extends NovaResource
{
	/**
	 * Build a "detail" query for the given resource.
	 *
	 * @param NovaRequest $request
	 * @param  \Illuminate\Database\Eloquent\Builder   $query
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public static function detailQuery(NovaRequest $request, $query)
	{
		$user = $request->user();
		
		// User Can View all Entries and is not restricted to its own
		if (!$user->hasPermissionTo(Str::replace("-",' ',parent::uriKey()) . '.view')) {
			return $query;
		}
		
		return parent::detailQuery($request, $query->where('user_id', $user->id));
	}
	
	/**
	 * Build an "index" query for the given resource.
	 *
	 * @param NovaRequest $request
	 * @param  \Illuminate\Database\Eloquent\Builder   $query
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public static function indexQuery(NovaRequest $request, $query)
	{
		$user = $request->user();
		
		// If the User has only Permission to view his own Entries, we scope the query.
		if ($user->hasPermissionTo(Str::replace("-",' ',parent::uriKey()) . '.view')) {
			return $query->where('user_id', $user->id);
		}
		
		return $query;
	}
	
	/**
	 * Build a "relatable" query for the given resource.
	 *
	 * This query determines which instances of the model may be attached to other resources.
	 *
	 * @param NovaRequest $request
	 * @param  \Illuminate\Database\Eloquent\Builder   $query
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public static function relatableQuery(NovaRequest $request, $query)
	{
		$user = $request->user();
		
		// User Can View all Entries and is not restricted to its own
		if (!$user->hasPermissionTo(Str::replace("-",' ',parent::uriKey()) . '.view')) {
			return parent::relatableQuery($request, $query);
		}
		
		return parent::relatableQuery($request, $query->where('user_id', $user->id));
	}
	
	/**
	 * Build a Scout search query for the given resource.
	 *
	 * @param NovaRequest $request
	 * @param  Builder                  $query
	 *
	 * @return Builder
	 */
	public static function scoutQuery(NovaRequest $request, $query)
	{
		$user = $request->user();
		
		// User Can View all Entries and is not restricted to its own
		if ($user->hasPermissionTo(Str::replace("-",' ',parent::uriKey()) . '.view')) {
			return $query;
		}
		
		return $query->where('user_id', $user->id);
	}
}