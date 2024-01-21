<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public $request;
    public $data;

    /**
     * Constructer
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->data['required'] = '<span class="text-danger">*</span>';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['title'] = "Roles List";
        $this->data['roles'] = Role::latest()->get();

        return view('admin.Roles.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        // $user = User::where(['id' => 1])->first();
        // $user->assignRole('admin');
        // $role = Role::where(['name' => 'manager'])->first();
        // Permission::firstOrCreate(['name' => 'add-service']);
        // Permission::firstOrCreate(['name' => 'edit-service']);
        // Permission::firstOrCreate(['name' => 'delete-service']);
        // $role->syncPermissions(['add-service', 'edit-service', 'delete-service']);

        // $user = User::whereId(1)->first();
        // $user->syncRoles([]);
        // $user->assignRole('manager'); selected_permissions
        // dd($user);
        // exit;

        $this->data['title'] = "Add Role";
        $this->data['mode'] = 'store';
        $this->data['components'] = get_component_names();
        $selectedPermissions = [];
        if (!empty($id)) {
	    $this->data['title'] = "Edit Role";
            $role = Role::where('id', $id)->first();
            $permissions = $role->permissions;
            // 
            foreach ($permissions as $permission) {
                $selectedPermissions[] = $permission->name;
            }
            $this->data += $role->toArray();
            // dd($this->data);
        }
        $this->data['selected_permissions'] = $selectedPermissions;
        return view('admin.Roles.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validate = \Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array|min:1'
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            try {
                $role = Role::firstOrCreate(['name' => $request->name]);
                if (!empty($request->permissions)) {
                    foreach ($request->permissions as $key => $permission) {
                        Permission::firstOrCreate(['name' => $permission]);
                    }
                    $role->syncPermissions($request->permissions);
                }
                $message = "Role successfully created.";
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
            }
            session()->flash('message', ['success' => $message]);
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = \Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array|min:1'
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            try {
                // update role name
                Role::whereId($id)->update([
                    "name" => $request->name
                ]);
                // Find the role by name
                $role = Role::firstOrCreate(['name' => $request->name]);
                // Remove all permissions from the role
                $role->syncPermissions([]);
                if (!empty($request->permissions)) {
                    foreach ($request->permissions as $key => $permission) {
                        Permission::firstOrCreate(['name' => $permission]);
                    }
                    $role->syncPermissions($request->permissions);
                }
                $message = "Role successfully updated.";
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
            }
            session()->flash('message', ['success' => $message]);
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::whereId($id)->first();
        // Remove all permissions from the role
        $role->syncPermissions([]);
        if (Role::find($id)->delete()) {
            session()->flash('message', ['success' => 'Role was deleted successfully']);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went wrong']);
        return redirect()->back();
    }
}
