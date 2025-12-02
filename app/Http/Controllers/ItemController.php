<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        return view('items.index');
    }

    public function create()
    {
        // Generate next code
        $nextCode = \App\Helpers\Helper::generateCode('ITM', Item::class);
        return view('items.create', compact('nextCode'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'code' => 'required|unique:items,code',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240' // max 10MB before resize
        ]);

        $maxAttempts = 5;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                return \DB::transaction(function () use ($r) {
                    $data = [
                        'code' => \App\Helpers\Helper::generateCode('ITM', Item::class),
                        'name' => $r->name,
                        'price' => $r->price
                    ];

                    // Handle image upload with resize
                    if ($r->hasFile('image')) {
                        $image = $r->file('image');
                        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                        // Resize and compress image to max 100KB
                        $img = Image::read($image->getRealPath());

                        // Start with reasonable dimensions
                        $width = 800;
                        $quality = 85;

                        // Resize and encode
                        $img->scale(width: $width);
                        $encoded = $img->encodeByMediaType('image/jpeg', quality: $quality);

                        // Reduce quality until size is under 100KB
                        while (strlen($encoded) > 102400 && $quality > 20) {
                            $quality -= 5;
                            if ($width > 400) {
                                $width -= 50;
                                $img = Image::read($image->getRealPath());
                                $img->scale(width: $width);
                            }
                            $encoded = $img->encodeByMediaType('image/jpeg', quality: $quality);
                        }

                        // Save to storage
                        Storage::disk('public')->put('items/' . $filename, $encoded);
                        $data['image'] = 'items/' . $filename;
                    }

                    Item::create($data);
                    return redirect()->route('items.index')->with('success', 'Item berhasil ditambahkan');
                });
            } catch (\Illuminate\Database\QueryException $e) {
                // If duplicate key error, retry
                if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                    $attempt++;
                    if ($attempt >= $maxAttempts) {
                        throw $e;
                    }
                    usleep(100000); // 100ms
                    continue;
                }
                throw $e;
            }
        }
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $r, Item $item)
    {
        $r->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        $data = [
            'name' => $r->name,
            'price' => $r->price
        ];

        // Handle image upload with resize
        if ($r->hasFile('image')) {
            // Delete old image
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }

            $image = $r->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Resize and compress image to max 100KB
            $img = Image::read($image->getRealPath());

            // Start with reasonable dimensions
            $width = 800;
            $quality = 85;

            // Resize and encode
            $img->scale(width: $width);
            $encoded = $img->encodeByMediaType('image/jpeg', quality: $quality);

            // Reduce quality until size is under 100KB
            while (strlen($encoded) > 102400 && $quality > 20) {
                $quality -= 5;
                if ($width > 400) {
                    $width -= 50;
                    $img = Image::read($image->getRealPath());
                    $img->scale(width: $width);
                }
                $encoded = $img->encodeByMediaType('image/jpeg', quality: $quality);
            }

            // Save to storage
            Storage::disk('public')->put('items/' . $filename, $encoded);
            $data['image'] = 'items/' . $filename;
        }

        $item->update($data);
        return redirect()->route('items.index')->with('success', 'Item updated successfully');
    }

    public function destroy(Item $item)
    {
        // Delete image if exists
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }
}
