<?php

namespace App\Http\Controllers;

use App\Models\Situs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminIdentitasSitusController extends Controller
{
    public function index()
    {
        return view('admin.identitas-situs.index', [
            'situs' => Situs::first()
        ]);
    }

    public function update(Request $request, $id)
    {
        $situs = Situs::find($id);

        $validator = Validator::make($request->all(), [
            'nm_desa'   => 'required',
            'kecamatan' => 'required',
            'kabupaten' => 'required',
            'provinsi'  => 'required',
            'kode_pos'  => 'required',
        ], [
            'nm_desa.required'   => 'Wajib menambahkan nama desa!',
            'kecamatan.required' => 'Wajib menambahkan kecamatan!',
            'kabupaten.required' => 'Wajib menambahkan kabupaten!',
            'provinsi.required'  => 'Wajib menambahkan provinsi!',
            'kode_pos.required'  => 'Wajib menambahkan kode pos!',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Default logo = yang lama
        $logo = $situs->logo;

        // Jika ada file logo diupload
        if ($request->hasFile('logo')) {
            // Hapus file lama jika ada dan file benar-benar ada di storage
            if ($situs->logo && Storage::disk('public')->exists($situs->logo)) {
                Storage::disk('public')->delete($situs->logo);
            }

            // Simpan file baru
            $file = $request->file('logo');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $logo = $file->storeAs('img-logo', $fileName, 'public');
        }

        // Update ke database
        $situs->update([
            'logo'      => $logo,
            'nm_desa'   => $request->nm_desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi'  => $request->provinsi,
            'kode_pos'  => $request->kode_pos,
        ]);

        return redirect('/admin/identitas-situs')->with('success', 'Berhasil memperbarui identitas situs');
    }
}
