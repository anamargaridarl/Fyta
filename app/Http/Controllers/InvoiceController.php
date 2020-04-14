<?php

namespace App\Http\Controllers;

class InvoiceController extends Controller{
    public function invoice()
    {
        return view('pages.invoice', ['order_number' => '125877', 'date' => 'Dec 24 2019', 'name' => 'Ellie Black', 'address' => 'Marcombe Dr NE, 334 3rd floor', 'location' => 'Calgary, Canada', 'sum' => '47.60€', 'delivery' => 'FREE', 'items' => [['img' => "img/sativa_indoor.jpg", 'name' => "Sativa Prime", 'price' => "4.20€", 'qty' => 3], ['img' => "img/supreme_vase.jpg", 'name' => "Supreme Bonsai Pot", 'price' => "40€", 'qty' => 1], ['img' => "img/watercan_tool.jpg", 'name' => "Green Watercan 12l", 'price' => "5€", 'qty' => 1]]]);
    }
}