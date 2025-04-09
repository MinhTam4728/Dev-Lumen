<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Quản lý khách hàng",
 *     description="API cho quản lý khách hàng"
 * )
 */
class CustomerController extends Controller
{
    public function __construct(private JWTAuth $jwtAuth) {}
/**
 * @OA\Get(
 *     path="/admin/customers",
 *     summary="Lấy danh sách khách hàng",
 *     tags={"Quản lý khách hàng"},
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         required=false,
 *         description="Tìm kiếm theo tên hoặc email khách hàng",
 *         @OA\Schema(type="string", example="Nguyen Van A")
 *     ),
 *     @OA\Parameter(
 *         name="sort",
 *         in="query",
 *         required=false,
 *         description="Sắp xếp theo ngày tạo (asc hoặc desc)",
 *         @OA\Schema(type="string", example="desc")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         required=false,
 *         description="Số lượng khách hàng trên mỗi trang",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lấy danh sách khách hàng thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Lấy danh sách thành công"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Nguyen Van A"),
 *                         @OA\Property(property="email", type="string", example="example@gmail.com"),
 *                         @OA\Property(property="created_at", type="string", example="2025-03-31 12:00:00"),
 *                         @OA\Property(property="updated_at", type="string", example="2025-03-31 12:00:00")
 *                     )
 *                 ),
 *                 @OA\Property(property="total", type="integer", example=100),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="last_page", type="integer", example=10)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
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
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách khách hàng thành công',
            'data' => $customers
        ], 200);
    }

    


/**
 * @OA\Get(
 *     path="/admin/orders",
 *     summary="Lấy tất cả đơn hàng và thông tin khách hàng",
 *     tags={"Quản lý khách hàng"},
 *     @OA\Parameter(
 *         name="order_id",
 *         in="query",
 *         required=false,
 *         description="ID của đơn hàng để tìm kiếm",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         required=false,
 *         description="Tên khách hàng để tìm kiếm (khớp một phần)",
 *         @OA\Schema(type="string", example="Nguyen")
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         required=false,
 *         description="Email khách hàng để tìm kiếm (khớp một phần)",
 *         @OA\Schema(type="string", example="example@gmail.com")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         required=false,
 *         description="Số lượng đơn hàng trên mỗi trang",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lấy danh sách đơn hàng thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Lấy danh sách đơn hàng thành công"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="total", type="number", example=150000),
 *                         @OA\Property(property="created_at", type="string", example="2025-03-31 12:00:00"),
 *                         @OA\Property(property="customer", type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Nguyen Van A"),
 *                             @OA\Property(property="email", type="string", example="example@gmail.com")
 *                         )
 *                     )
 *                 ),
 *                 @OA\Property(property="total", type="integer", example=50),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="last_page", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
public function allOrders(Request $request)
{
    try {
        // Lấy các tham số tìm kiếm từ query
        $orderId = $request->query('order_id');
        $name = $request->query('name');
        $email = $request->query('email');
        $perPage = $request->query('per_page', 10);

        
        $query = \App\Models\Order::with(['customer' => function ($query) {
            $query->select('id', 'name', 'email');
        }]);

        
        if ($orderId) {
            $query->where('id', $orderId);
        }

   
        if ($name) {
            $query->whereHas('customer', function ($q) use ($name) {
                $q->where('name', 'like', "%$name%");
            });
        }

      
        if ($email) {
            $query->whereHas('customer', function ($q) use ($email) {
                $q->where('email', 'like', "%$email%");
            });
        }

        $orders = $query->paginate($perPage);

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Không tìm thấy đơn hàng nào',
                'data' => [
                    'current_page' => 1,
                    'data' => [],
                    'total' => 0,
                    'per_page' => $perPage,
                    'last_page' => 1
                ]
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách đơn hàng thành công',
            'data' => $orders
        ], 200);
    } catch (\Exception $e) {
        \Log::error('AllOrders error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Lỗi hệ thống'
        ], 500);
    }
}


   /**
 * @OA\Post(
 *     path="/admin/customers",
 *     summary="Tạo khách hàng mới",
 *     tags={"Quản lý khách hàng"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password"},
 *             @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
 *             @OA\Property(property="email", type="string", example="example@gmail.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Tạo khách hàng thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Tạo tài khoản thành công!"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Dữ liệu không hợp lệ - Lỗi xác thực",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi xác thực"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được dài quá 255 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.string' => 'Email phải là chuỗi ký tự.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được dài quá 255 ký tự.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);
    

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 1 
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tạo tài khoản thành công!',
            'data' => $customer
        ], 201);
    }

    /**
 * @OA\Put(
 *     path="/admin/customers/{id}",
 *     summary="Cập nhật thông tin khách hàng",
 *     tags={"Quản lý khách hàng"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID của khách hàng",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
 *          
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Cập nhật khách hàng thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Cập nhật thành công"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Không tìm thấy khách hàng",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Không tìm thấy khách hàng")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:6',
        ], [
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được dài quá 255 ký tự.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer->update([
            'name' => $request->name ?? $customer->name,
            'password' => $request->password ? Hash::make($request->password) : $customer->password,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin khách hàng thành công',
            'data' => $customer
        ], 201);
    }


    /**
 * @OA\Put(
 *     path="/admin/change-password",
 *     summary="Thay đổi mật khẩu của admin",
 *     tags={"Quản lý khách hàng"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"old_password", "new_password"},
 *             @OA\Property(property="old_password", type="string", example="123456789"),
 *             @OA\Property(property="new_password", type="string", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Thay đổi mật khẩu thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Thay đổi mật khẩu thành công")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Chưa xác thực hoặc mật khẩu cũ không đúng",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Mật khẩu cũ không đúng")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Lỗi xác thực dữ liệu",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi xác thực"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
public function changePassword(Request $request)
{
    try {
        // Authenticate the admin using the injected JWTAuth instance
        $admin = $this->jwtAuth->parseToken()->authenticate();

        // Ensure the user is an admin (redundant due to middleware, but added for safety)
        if ($admin->role !== 0) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ admin mới có thể sử dụng chức năng này'
            ], 403);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ], [
            'old_password.required' => 'Mật khẩu cũ là bắt buộc.',
            'old_password.string' => 'Mật khẩu cũ phải là chuỗi ký tự.',
            'new_password.required' => 'Mật khẩu mới là bắt buộc.',
            'new_password.string' => 'Mật khẩu mới phải là chuỗi ký tự.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if the old password matches
        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu cũ không đúng'
            ], 401);
        }

        // Update the password
        $admin->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thay đổi mật khẩu thành công'
        ], 201);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token đã hết hạn'
        ], 401);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token không hợp lệ'
        ], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token không được cung cấp'
        ], 401);
    } catch (\Exception $e) {
        \Log::error('ChangePassword error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Lỗi hệ thống'
        ], 500);
    }
}



    /**
 * @OA\Delete(
 *     path="/admin/customers/{id}",
 *     summary="Xóa khách hàng",
 *     tags={"Quản lý khách hàng"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID của khách hàng",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Xóa khách hàng thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Xóa khách hàng thành công")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Không tìm thấy khách hàng",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Không tìm thấy khách hàng")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Không thể xóa khách hàng có đơn hàng",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Không thể xóa khách hàng có đơn hàng")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }
        if ($customer->orders()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa khách hàng có đơn hàng'
            ], 400);
        }

        $customer->delete();
        return response()->json([
            'status' => true,
            'message' => 'Xóa khách hàng thành công'
        ], 200);
    }

   /**
 * @OA\Get(
 *     path="/customer/info",
 *     summary="Lấy thông tin khách hàng hiện tại",
 *     tags={"Thông tin khách hàng"},
 *     @OA\Response(
 *         response=201,
 *         description="Lấy thông tin khách hàng thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Lấy thông tin thành công"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Chưa xác thực",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Chưa xác thực")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
public function show()
{
    try {
        // Xác thực token và lấy thông tin khách hàng
        $customer = $this->jwtAuth->parseToken()->authenticate();

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin khách hàng thành công',
            'data' => $customer
        ], 200);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token đã hết hạn'
        ], 401);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token không hợp lệ'
        ], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token không được cung cấp'
        ], 401);
    } catch (\Exception $e) {
        \Log::error('Show error: ' . $e->getMessage()); // Ghi log lỗi để kiểm tra
        return response()->json([
            'status' => false,
            'message' => 'Đã xảy ra lỗi hệ thống'
        ], 500);
    }
}

    /**
 * @OA\Put(
 *     path="/customer",
 *     summary="Cập nhật thông tin cá nhân khách hàng hiện tại",
 *     tags={"Thông tin khách hàng"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
 *            
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Cập nhật thông tin thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Cập nhật thông tin thành công"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Chưa xác thực",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Chưa xác thực")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
    public function updateProfile(Request $request)
    {
        try {
            $customer = $this->jwtAuth->parseToken()->authenticate();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                
            ], [
                'name.string' => 'Tên phải là chuỗi ký tự.',
                'name.max' => 'Tên không được dài quá 255 ký tự.',
               
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lỗi xác thực',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customer->update([
                'name' => $request->name ?? $customer->name,
             
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thông tin cá nhân thành công',
                'data' => $customer
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa xác thực'
            ], 401);
        }
    }

    /**
 * @OA\Put(
 *     path="/customer/change-password",
 *     summary="Thay đổi mật khẩu của khách hàng",
 *     tags={"Thông tin khách hàng"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"old_password", "new_password"},
 *             @OA\Property(property="old_password", type="string", example="password123"),
 *             @OA\Property(property="new_password", type="string", example="newpassword456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Thay đổi mật khẩu thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Thay đổi mật khẩu thành công")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Chưa xác thực hoặc mật khẩu cũ không đúng",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Mật khẩu cũ không đúng")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Lỗi xác thực dữ liệu",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi xác thực"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
public function changePasswordCustomer(Request $request)
{
    try {
        // Authenticate the customer using the injected JWTAuth instance
        $customer = $this->jwtAuth->parseToken()->authenticate();

        // Ensure the user is a customer (redundant due to middleware, but added for safety)
        if ($customer->role !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ khách hàng mới có thể sử dụng chức năng này'
            ], 403);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ], [
            'old_password.required' => 'Mật khẩu cũ là bắt buộc.',
            'old_password.string' => 'Mật khẩu cũ phải là chuỗi ký tự.',
            'new_password.required' => 'Mật khẩu mới là bắt buộc.',
            'new_password.string' => 'Mật khẩu mới phải là chuỗi ký tự.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if the old password matches
        if (!Hash::check($request->old_password, $customer->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu cũ không đúng'
            ], 401);
        }

        // Update the password
        $customer->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thay đổi mật khẩu thành công'
        ], 201);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token đã hết hạn'
        ], 401);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token không hợp lệ'
        ], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Token không được cung cấp'
        ], 401);
    } catch (\Exception $e) {
        \Log::error('ChangePassword error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Lỗi hệ thống'
        ], 500);
    }
}

   /**
 * @OA\Get(
 *     path="/customer/orders",
 *     summary="Lấy danh sách đơn hàng của khách hàng hiện tại",
 *     tags={"Danh sách đơn hàng"},
 *     @OA\Response(
 *         response=200,
 *         description="Lấy danh sách đơn hàng thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Lấy danh sách đơn hàng thành công"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Chưa xác thực",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Chưa xác thực")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
    public function orders()
    {
        try {
            $customer = $this->jwtAuth->parseToken()->authenticate();
            $orders = $customer->orders;
    
            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Không tìm thấy đơn hàng nào',
                    'data' => []
                ], 201);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách đơn hàng thành công',
                'data' => $orders
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token đã hết hạn'
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token không hợp lệ'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa xác thực'
            ], 401);
        }
    }
}