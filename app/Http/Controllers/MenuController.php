<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    public function index()
    {
        $data['title'] = 'Menu / Create';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        $data['menus'] = Menu::whereNull('parent_id')->get();
        $data['permissions'] = Permission::all();

        return view('menus.index', $data);
    }

    public function create()
    {
        $permissions = Permission::pluck('name', 'id');
        $parents = Menu::pluck('title', 'id');
        return view('menus.create', compact('permissions', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Menu::create($request->all());
        return redirect()->route('admin.menus.index')->with('success', 'Menu added successfully');
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $permissions = Permission::pluck('name', 'id');
        $parents = Menu::where('id', '!=', $id)->pluck('title', 'id');
        return view('menus.edit', compact('menu', 'permissions', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($request->all());
        return redirect()->route('menus.index')->with('success', 'Menu updated successfully');
    }

    public function destroy($id)
    {
        Menu::findOrFail($id)->delete();
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully');
    }
}
