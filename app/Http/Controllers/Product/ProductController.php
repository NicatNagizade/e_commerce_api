<?php

namespace App\Http\Controllers\Product;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('getProducts');
    }

    public function getProducts()
    {
        $validator = validator(request()->all(),[
            'name' => 'nullable|string',
            'gender' => 'nullable|string|in:male,female,unisex',
            'child' => 'nullable|boolean',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'manufacturer_id' => 'nullable|integer',
            'sub_category_id' => 'nullable|integer',
            'sub_product_type_id' => 'nullable|integer',
            'size_id' => 'nullable|integer',
            'color_id' => 'nullable|integer',
            'has_discount' => 'nullable|boolean'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $products = Product::with(['images','sizes','colors',
            'discount'=>function($q){
                $q->where('start','<=',now())->where('end','>=', now());
            }])
            ->when(request('name'), function($q){
                $q->where('name','like', '%'.request('name').'%');
            })
            ->when(request('gender'), function($q){
                $q->where('gender',request('gender'));
            })
            ->when(request('child') !== null, function($q){
                $q->where('child',request('child'));
            })
            ->when(request('min_price'), function($q){
                $q->where('price','>=',request('min_price'));
            })
            ->when(request('max_price'), function($q){
                $q->where('price','<=',request('max_price'));
            })
            ->when(request('manufacturer_id'), function($q){
                $q->where('manufacturer_id',request('manufacturer_id'));
            })
            ->when(request('sub_category_id'), function($q){
                $q->where('sub_category_id',request('sub_category_id'));
            })
            ->when(request('sub_product_type_id'), function($q){
                $q->where('sub_product_type_id',request('sub_product_type_id'));
            })
            ->when(request('size_id'), function($q){
                $q->whereHas('sizes', function($q){
                    $q->where('sizes.id',request('size_id'));
                });
            })
            ->when(request('color_id'), function($q){
                $q->whereHas('colors', function($q){
                    $q->where('colors.id',request('color_id'));
                });
            })
            ->when(request('has_discount'), function($q){
                $q->whereHas('discount', function($q){
                    $q->where('start','<=',now())->where('end','>=', now());
                });
            })
            ->paginate(10);
        return $this->sendSuccess($products);
    }

    public function createProduct()
    {
        $validator = validator(request()->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric|between:0.01,999999.99',
            'image' => 'required|file|image|max:10000',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'gender' => 'required|string|in:male,female,unisex',
            'child' => 'nullable|boolean',
            'manufacturer_id' => 'required|integer|exists:manufacturers,id',
            'sub_category_id' => 'required|integer|exists:sub_categories,id',
            'sub_product_type_id' => 'required|integer|exists:sub_product_types,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|image|max:10000',
            'sizes' => 'required|array',
            'sizes.*' => 'required|integer|exists:sizes,id',
            'colors' => 'required|array',
            'colors.*' => 'required|integer|exists:colors,id',
            'discount_price' => 'nullable|numeric|between:0.01,999999.99',
            'discount_start_date' => 'nullable|date_format:Y-m-d H:i',
            'discount_end_date' => 'nullable|date_format:Y-m-d H:i'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        DB::beginTransaction();
        try {
            $image = new FileHelper(request('image'));
            $product = new Product;
            $product->name = request('name');
            $product->image = $image->getName();
            $product->price = request('price');
            $product->description = request('description');
            $product->content = request('content');
            $product->gender = request('gender');
            $product->child = request('child') ?: false;
            $product->manufacturer_id = request('manufacturer_id') ?: false;
            $product->sub_category_id = request('sub_category_id') ?: false;
            $product->sub_product_type_id = request('sub_product_type_id') ?: false;
            $product->save();
            $product->createLog();
            $product->sizes()->attach(request('sizes'));
            $product->colors()->attach(request('colors'));
            if(request('discount_price')){
                if(!request('discount_end_date')){
                    return $this->sendError('Endirim elave etdikde bitme vaxti qeyd olunmalidir');
                }
                $product->addDiscount(
                    request('discount_price'), 
                    request('discount_start_date'), 
                    request('discount_end_date')
                );
            }
            $product->addSubImages(request('images'));
            $image->save(Product::IMAGE_PATH);
            DB::commit();
            return $this->sendSuccess(['inserted_id' => $product->id]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    public function updateProduct($id)
    {
        $validator = validator(request()->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric|between:0.01,999999.99',
            'image' => 'nullable|file|image|max:10000',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'gender' => 'required|string|in:male,female,unisex',
            'child' => 'nullable|boolean',
            'manufacturer_id' => 'required|integer|exists:manufacturers,id',
            'sub_category_id' => 'required|integer|exists:sub_categories,id',
            'sub_product_type_id' => 'required|integer|exists:sub_product_types,id',
            'sizes' => 'required|array',
            'sizes.*' => 'required|integer|exists:sizes,id',
            'colors' => 'required|array',
            'colors.*' => 'required|integer|exists:colors,id',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        DB::beginTransaction();
        try {
            $image = new FileHelper(request('image'));
            $product = Product::findOrFail($id);
            $product->name = request('name');
            if($image->has()){
                $product->deleteImage();
                $product->image = $image->getName();
            }
            $product->price = request('price');
            $product->description = request('description');
            $product->content = request('content');
            $product->gender = request('gender');
            $product->child = request('child') ?: false;
            $product->manufacturer_id = request('manufacturer_id') ?: false;
            $product->sub_category_id = request('sub_category_id') ?: false;
            $product->sub_product_type_id = request('sub_product_type_id') ?: false;
            $product->save();
            $product->createLog('updated');
            $product->sizes()->sync(request('sizes'));
            $product->colors()->sync(request('colors'));
            $image->save(Product::IMAGE_PATH);
            DB::commit();
            return $this->sendSuccess();
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    public function deleteProduct($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->createLog('deleted');
            $product->deleteImages();
            $product->delete();
            DB::commit();
            return $this->sendSuccess();
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }
}
