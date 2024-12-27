<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        // Mendapatkan semua data customer dengan pagination
        $customers = Customer::latest()->paginate(5);
        return new CustomerResource(true, 'List Data Customers', $customers);
    }

    public function store(Request $request)
    {
        // Melakukan validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|min:6',
            'notelp'   => 'required',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat customer baru
        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'notelp'   => $request->notelp,
        ]);

        // Mengembalikan response sukses
        return new CustomerResource(true, 'Customer Successfully Created!', $customer);
    }

    public function show($id)
    {
        // Mencari customer berdasarkan ID
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer Not Found'], 404);
        }

        return new CustomerResource(true, 'Customer Details', $customer);
    }

    public function update(Request $request, $id)
    {
        // Melakukan validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:customers,email,' . $id,
            'password' => 'nullable|min:6',
            'notelp'   => 'required',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mencari customer berdasarkan ID
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer Not Found'], 404);
        }

        // Mengupdate data customer
        $customer->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $customer->password,
            'notelp'   => $request->notelp,
        ]);

        return new CustomerResource(true, 'Customer Successfully Updated!', $customer);
    }

    public function destroy($id)
    {
        // Mencari customer berdasarkan ID
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer Not Found'], 404);
        }

        // Menghapus customer
        $customer->delete();

        return new CustomerResource(true, 'Customer Successfully Deleted!', null);
    }
}
