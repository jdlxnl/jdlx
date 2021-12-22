<?php

namespace App\Generated\User;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Jdlx\Http\Controllers\Traits\CanFilterSortAndPage;

/**
 * @mixin AuthorizesRequests
 */
trait WithUserCrudRoutes
{
    use CanFilterSortAndPage;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $items = User::query()->with([]);
        $paging = $this->filterSortAndPage($request, $items, User::getFilterableFields());
        $data = $paging->jsonSerialize();

        $resource = $this->getResourceClass();
        $data["items"] = call_user_func($resource . '::collection', $paging->items());
        return response()->success($data, 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', [User::class]);

        $request->validate($this->getValidationRules("create"));

        $fields = $request->only(User::getWritableFields());

        $user = new User($fields);
        $user->save();

        $resource = $this->getResourceClass();
        return response()->success(new $resource($user), 201);
    }


    public function show(User $user)
    {
        $this->authorize('view', $user);

        $resource = $this->getResourceClass();
        return response()->success(new $resource($user));
    }


    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate($request->validate($this->getValidationRules("update")));
        $fields = $request->only(User::getEditableFields());
        $user->fill($fields)->save();
        $resource = $this->getResourceClass();
        return response()->success(new $resource($user));
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
        return response()->success([
            "id" => $user->id,
            "deleted" => true
        ], 200);
    }

    protected function getValidationRules($for = "create")
    {
        return UserFields::validation($for);
    }

    /**
     * Return the resource class to be used to return responses
     * defaults to the generated resource, but can be overwritten
     * to alter responses
     *
     * @return string
     */
    protected function getResourceClass(): string
    {
        return UserResource::class;
    }
}
