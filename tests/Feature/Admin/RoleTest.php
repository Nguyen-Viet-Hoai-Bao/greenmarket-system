<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PermissionImport;
use App\Exports\PermissionExport;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Tạo admin giả và giả lập đăng nhập guard admin
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');
    }

    // =========== PERMISSION =============

    public function test_all_permission_page_loads()
    {
        Permission::create(['name' => 'test.permission', 'group_name' => 'group1', 'guard_name' => 'admin']);

        $response = $this->get(route('all.permission'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.permission.all_permission');
        $response->assertViewHas('permissions');
    }

    public function test_add_permission_page_loads()
    {
        $response = $this->get(route('add.permission'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.permission.add_permission');
    }

    public function test_store_permission_saves_and_redirects()
    {
        $response = $this->post(route('permission.store'), [
            'name' => 'new.permission',
            'group_name' => 'testgroup',
        ]);

        $response->assertRedirect(route('all.permission'));
        $response->assertSessionHas('message', 'Permission Created Successfully');

        $this->assertDatabaseHas('permissions', [
            'name' => 'new.permission',
            'group_name' => 'testgroup',
            'guard_name' => 'admin',
        ]);
    }

    public function test_edit_permission_page_loads()
    {
        $permission = Permission::create(['name' => 'edit.permission', 'group_name' => 'group1', 'guard_name' => 'admin']);

        $response = $this->get(route('edit.permission', $permission->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.permission.edit_permission');
        $response->assertViewHas('permission', $permission);
    }

    public function test_update_permission_updates_and_redirects()
    {
        $permission = Permission::create(['name' => 'old.permission', 'group_name' => 'group1', 'guard_name' => 'admin']);

        $response = $this->post(route('permission.update'), [
            'id' => $permission->id,
            'name' => 'updated.permission',
            'group_name' => 'updatedgroup',
        ]);

        $response->assertRedirect(route('all.permission'));
        $response->assertSessionHas('message', 'Permission Update Successfully');

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'updated.permission',
            'group_name' => 'updatedgroup',
        ]);
    }

    public function test_delete_permission_deletes_and_redirects()
    {
        $permission = Permission::create(['name' => 'delete.permission', 'group_name' => 'group1', 'guard_name' => 'admin']);

        $response = $this->get(route('delete.permission', $permission->id));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Permission Delete Successfully');

        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_import_permission_page_loads()
    {
        $response = $this->get(route('import.permission'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.permission.import_permission');
    }

    public function test_export_permission_download()
    {
        Excel::fake();

        $response = $this->get(route('export'));

        $response->assertStatus(200);
    }

    // =========== ROLES ================

    public function test_all_roles_page_loads()
    {
        Role::create(['name' => 'testrole', 'guard_name' => 'admin']);

        $response = $this->get(route('all.roles'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.role.all_roles');
        $response->assertViewHas('roles');
    }

    public function test_add_roles_page_loads()
    {
        $response = $this->get(route('add.roles'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.role.add_roles');
    }

    public function test_store_roles_saves_and_redirects()
    {
        $response = $this->post(route('roles.store'), [
            'name' => 'newrole',
        ]);

        $response->assertRedirect(route('all.roles'));
        $response->assertSessionHas('message', 'Role Creted Successfully');

        $this->assertDatabaseHas('roles', [
            'name' => 'newrole',
            'guard_name' => 'admin',
        ]);
    }

    public function test_edit_roles_page_loads()
    {
        $role = Role::create(['name' => 'editrole', 'guard_name' => 'admin']);

        $response = $this->get(route('edit.roles', $role->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.role.edit_roles');
        $response->assertViewHas('roles', $role);
    }

    public function test_update_roles_updates_and_redirects()
    {
        $role = Role::create(['name' => 'oldrole', 'guard_name' => 'admin']);

        $response = $this->post(route('roles.update'), [
            'id' => $role->id,
            'name' => 'updatedrole',
        ]);

        $response->assertRedirect(route('all.roles'));
        $response->assertSessionHas('message', 'Role Updated Successfully');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updatedrole',
        ]);
    }

    public function test_delete_roles_deletes_and_redirects()
    {
        $role = Role::create(['name' => 'deleterole', 'guard_name' => 'admin']);

        $response = $this->get(route('delete.roles', $role->id));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Role Deleted Successfully');

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    // =========== RoleControllerTest ================
    public function test_add_roles_permission_view_is_loaded()
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        Permission::create(['name' => 'edit articles', 'group_name' => 'Articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete articles', 'group_name' => 'Articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'publish articles', 'group_name' => 'Articles', 'guard_name' => 'web']);

        $response = $this->get(route('add.roles.permission'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.rolesetup.add_roles_permission');
        $response->assertViewHasAll(['roles', 'permissions', 'permission_groups']);
    }

    public function test_role_permission_store()
    {
        $role = Role::create(['name' => 'author', 'guard_name' => 'web']);

        $permission1 = Permission::create(['name' => 'create posts', 'group_name' => 'Posts', 'guard_name' => 'web']);
        $permission2 = Permission::create(['name' => 'edit posts', 'group_name' => 'Posts', 'guard_name' => 'web']);
        $permission3 = Permission::create(['name' => 'delete posts', 'group_name' => 'Posts', 'guard_name' => 'web']);

        $permissions = collect([$permission1, $permission2, $permission3]);

        $response = $this->post(route('role.permission.store'), [
            'role_id' => $role->id,
            'permission' => $permissions->pluck('id')->toArray(),
        ]);

        foreach ($permissions as $permission) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        }

        $response->assertRedirect(route('all.roles.permission'));
        $response->assertSessionHas('message', 'Role Permission Added Successfully');
    }

    public function test_all_roles_permission_view()
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $response = $this->get(route('all.roles.permission'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.rolesetup.all_roles_permission');
        $response->assertViewHas('roles');
    }

    public function test_edit_roles_permission_view()
    {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        Permission::create(['name' => 'edit articles', 'group_name' => 'Articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete articles', 'group_name' => 'Articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'publish articles', 'group_name' => 'Articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage users', 'group_name' => 'Users', 'guard_name' => 'web']);
        Permission::create(['name' => 'view reports', 'group_name' => 'Reports', 'guard_name' => 'web']);

        $response = $this->get(route('admin.edit.roles', $role->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.rolesetup.edit_roles_permission');
        $response->assertViewHasAll(['role', 'permissions', 'permission_groups']);
    }

    // =========== AdminControllerTest ================

    public function test_all_admin_view()
    {
        Admin::factory()->count(3)->create();

        $response = $this->get(route('all.admin'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.admin.all_admin');
        $response->assertViewHas('alladmin');
    }

    public function test_add_admin_view()
    {
        Role::create(['name' => 'editor', 'guard_name' => 'web']);
        Role::create(['name' => 'author', 'guard_name' => 'web']);

        $response = $this->get(route('add.admin'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.admin.add_admin');
        $response->assertViewHas('roles');
    }

    public function test_admin_store()
    {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $response = $this->post(route('admin.store'), [
            'name' => 'John Admin',
            'email' => 'admin@gmail.com',
            'phone' => '123456',
            'address' => 'Hanoi',
            'password' => 'secret123',
            'roles' => [$role->id], 
        ]);

        $this->assertDatabaseHas('admins', ['email' => 'admin@gmail.com']);
        $response->assertRedirect(route('all.admin'));
        $response->assertSessionHas('message', 'New Admin Inserted Successfully');
    }


    public function test_edit_admin_view()
    {
        $admin = Admin::factory()->create();
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $response = $this->get(route('edit.admin', $admin->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.pages.admin.edit_admin');
        $response->assertViewHasAll(['roles', 'admin']);
    }
}
