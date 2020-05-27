<?php

use App\Models\Msk\AvailabilityOfProduct;
use App\Models\Msk\Category;
use App\Models\Msk\Color;
use App\Models\Msk\Country;
use App\Models\Msk\Coupon;
use App\Models\Msk\Currency;
use App\Models\Msk\Manufacturer;
use App\Models\Msk\Metro;
use App\Models\Msk\ProductType;
use App\Models\Msk\Region;
use App\Models\Msk\Size;
use App\Models\Msk\SubCategory;
use App\Models\Msk\SubProductType;
use App\Models\Msk\Tag;
use App\Models\Msk\TypeOfDelivery;
use App\Models\Msk\UserNotification;
use Illuminate\Database\Seeder;

class MskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['Azerbaijan','England','Turkey'];
        foreach($names as $name){
            $table = new Country;
            $table->name = $name;
            $table->save();
        }
        $names = ['Baki','Gence','Sumqayit'];
        foreach($names as $name){
            $table = new Region;
            $table->name = $name;
            $table->country_id = 1;
            $table->save();
        }
        $names = ['Elmler','Yasamal','Nizami'];
        foreach($names as $name){
            $table = new Metro;
            $table->name = $name;
            $table->region_id = 1;
            $table->save();
        }
        $names = ['Geyim', 'Aksesuar'];
        foreach($names as $name){
            $table = new Category;
            $table->name = $name;
            $table->save();
        }
        $names = ['Salvar', 'Koynek', 'Ayaqqabi'];
        foreach($names as $name){
            $table = new SubCategory;
            $table->name = $name;
            $table->category_id = 1;
            $table->save();
        }
        $names = ['Idman', 'Klassik', 'Polklassik'];
        foreach($names as $name){
            $table = new ProductType;
            $table->name = $name;
            $table->save();
        }
        $names = ['test1', 'test2', 'test3'];
        foreach($names as $name){
            $table = new SubProductType;
            $table->name = $name;
            $table->product_type_id = 1;
            $table->save();
        }
        $names = ['qirmizi', 'ag', 'qara'];
        foreach($names as $name){
            $table = new Color;
            $table->name = $name;
            $table->save();
        }
        $names = ['X', 'XL', 'XXL'];
        foreach($names as $name){
            $table = new Size;
            $table->name = $name;
            $table->save();
        }
        $names = ['ADIDAS', 'NIKE', 'PUMA'];
        foreach($names as $name){
            $table = new Manufacturer;
            $table->name = $name;
            $table->save();
        }
        $names = ['azn', 'rubl', 'dirhem'];
        foreach($names as $name){
            $table = new Currency;
            $table->name = $name;
            $table->save();
        }
        $names = ['Yeni', 'Endirimde'];
        foreach($names as $name){
            $table = new Tag;
            $table->name = $name;
            $table->save();
        }
        $names = ['Sade catdirilma' => 'Catdirilma Baki seheri erazisinde ...',
            'Express catdirilma' => 'Baki seheri ve rayonlara ...'];
        foreach($names as $name=>$content){
            $table = new TypeOfDelivery;
            $table->name = $name;
            $table->content = $content;
            $table->price = 1;
            $table->save();
        }
        $names = ['Elde', 'Anbarda'];
        foreach($names as $name){
            $table = new AvailabilityOfProduct;
            $table->name = $name;
            $table->save();
        }
        $names = ['Tapilmadi'=>'danger', 'Tapildi'=>'success'];
        foreach($names as $name=>$type){
            $table = new UserNotification;
            $table->content = $name;
            $table->type = $type;
            $table->save();
        }
        $coupon = new Coupon;
        $coupon->code = 'Paltar2020';
        $coupon->discount = 40;
        $coupon->percent = 1;
        $coupon->type = 'both';
        $coupon->start = now();
        $coupon->end = now()->addMonths(2);
        $coupon->save();
        
    }
}
