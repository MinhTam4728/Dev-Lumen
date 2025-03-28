<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Models\Customer;
use OpenApi\Annotations as OA;


class CustomerController extends Controller
{
    public function __construct(private JWTAuth $jwtAuth) {}

/**
 * @OA\Get(
 *     path="/admin/customers",
 *     summary="Get all customers",
 *     @OA\Response(
 *         response=200,
 *         description="List of customers",
 *         @OA\JsonContent(ref="#/components/schemas/Customer")
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
    /**
     * @OA\Schema(
     *     schema="Customer",
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="email", type="string"),
     * )
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortOrder = $request->query('sort', 'desc');
        $perPage = $request->query('per_page', 10);
        
        $query = Customer::query();
        if ($search) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }
        $query->orderBy('created_at', $sortOrder);
        
        $customers = $query->paginate($perPage);
        return response()->json($customers);
    }



    /**
     * @OA\Post(
     *     path="/admin/customers",
     *     summary="Create a new customer",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "role"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Customer created successfully"),
     *     @OA\Response(response=400, description="Invalid input"),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers',
            'password' => 'required|string|min:6',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'Customer created successfully', 'customer' => $customer], 201);
    }

    /**
     * @OA\Put(
     *     path="/admin/customers/{id}",
     *     summary="Update customer information",
     *     @OA\Parameter(name="id", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Customer updated successfully"),
     *     @OA\Response(response=404, description="Customer not found"),
     *      security={{"bearerAuth": {}}}
     * )
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $this->validate($request, [
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:6',
        ]);

        $customer->update([
            'name' => $request->name ?? $customer->name,
            'password' => $request->password ? bcrypt($request->password) : $customer->password,
        ]);

        return response()->json(['message' => 'Customer updated successfully', 'customer' => $customer]);
    }

    /**
     * @OA\Delete(
     *     path="/admin/customers/{id}",
     *     summary="Delete a customer",
     *     @OA\Parameter(name="id", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Customer deleted successfully"),
     *     @OA\Response(response=404, description="Customer not found"),
     *     @OA\Response(response=400, description="Cannot delete customer with orders"),security={{"bearerAuth": {}}}
     * )
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        if ($customer->orders()->count() > 0) {
            return response()->json(['message' => 'Cannot delete customer with orders'], 400);
        }

        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully'], 204);
    }

    /**
     * @OA\Get(
     *     path="/customer",
     *     summary="Get current customer information",
     *     @OA\Response(response=200, description="Customer information retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function show()
    {
        try {
            $customer = JWTAuth::parseToken()->authenticate();
            return response()->json($customer);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    /**
     * @OA\Put(
     *     path="/customer",
     *     summary="Update current customer profile",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile updated successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     * security={{"bearerAuth": {}}}
     * )
     */
    public function updateProfile(Request $request)
    {
        try {
            $customer = JWTAuth::parseToken()->authenticate();

            $this->validate($request, [
                'name' => 'sometimes|string|max:255',
                'password' => 'sometimes|string|min:6',
            ]);

            $customer->update([
                'name' => $request->name ?? $customer->name,
                'password' => $request->password ? bcrypt($request->password) : $customer->password,
            ]);

            return response()->json(['message' => 'Profile updated successfully', 'customer' => $customer]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/customer/orders",
     *     summary="Get current customer's orders",
     *     @OA\Response(response=200, description="Orders retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *      security={{"bearerAuth": {}}}
     * )
     */
    public function orders()
    {
        try {
            $customer = $this->jwtAuth->parseToken()->authenticate();
            $orders = $customer->orders;
    
            if ($orders->isEmpty()) {
                return response()->json(['message' => 'No orders found'], 200);
            }
    
            return response()->json($orders, 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}