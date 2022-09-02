<?php

namespace App\Http\Controllers;

use App\Models\Laptop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class laptopController extends Controller
{
    public function index()
    {
        //get posts
        $laptops = Laptop::latest()->paginate(5);

        //render view with posts
        return view('laptops.index', compact('laptops'));
    }

    public function create()
    {
        return view('laptops.create');
    }

    public function store(Request $request)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama'     => 'required|min:5',
            'tipe'   => 'required|min:2',
            'harga'   => 'required|min:5'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/laptops', $image->hashName());

        //create post
        Laptop::create([
            'image'     => $image->hashName(),
            'nama'     => $request->nama,
            'tipe'   => $request->tipe,
            'harga'   => $request->harga
        ]);

        //redirect to index
        return redirect()->route('laptops.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function edit(Laptop $laptop)
    {
        return view('laptops.edit', compact('laptop'));
    }

    public function update(Request $request, Laptop $laptop)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama'     => 'required|min:5',
            'tipe'   => 'required|min:2',
            'harga'   => 'required|min:5'

        ]);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/laptops', $image->hashName());

            //delete old image
            Storage::delete('public/laptops/'.$laptop->image);

            //update laptop with new image
            $laptop->update([
                'image'     => $image->hashName(),
                'nama'     => $request->nama,
                'tipe'   => $request->tipe,
                'harga'   => $request->harga
            ]);

        } else {

            //update laptop without image
            $laptop->update([
                'nama'     => $request->nama,
                'tipe'   => $request->tipe,
                'harga'   => $request->harga
            ]);
        }

        //redirect to index
        return redirect()->route('laptops.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(Laptop $laptop)
    {
        //delete image
        Storage::delete('public/laptops/'. $laptop->image);

        //delete laptop
        $laptop->delete();

        //redirect to index
        return redirect()->route('laptops.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
