<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DOMWrap\Document;
use DB;
use Carbon\Carbon;

class PriceFindScript extends Controller
{
    
    public function scrape(Request $req) {

     
        $urls = [ $req->link => [] ];
        
        
        $check_exist  = DB::table('urls')->where('url',$req->link)->exists();
        if (!$check_exist) { 

            foreach ($urls as $key => $u) {
                $response = Http::get($key);

                $doc = (new Document())->html($response->body());
                $nodes = $doc->find('span');
                $urls[$key]['tmp'][] = $this->getValues($nodes);
                $nodes = $doc->find('p');
                $urls[$key]['tmp'][] = $this->getValues($nodes); 

                foreach ($urls[$key]['tmp'] as $k => $p) {
                    if (count($p) > 0) {
                        foreach ($p as $pp) {
                            $urls[$key]['prices'][] = trim($pp); 
                        }

                    }
                }
                unset($urls[$key]['tmp']);

            }
             
            $insert = DB::table('urls')->insert([
                "url"           => $req->link,
                "created_at"    => Carbon::now()
            ]);

            if ($insert) {
                $find  = DB::table('urls')->where('url',$req->link)->latest()->first(); 
                DB::table('urlattributes')->insert([
                    "urlid"         => $find->id,
                    "price"         => $urls[$req->link]['prices'][0],
                    "created_at"    => Carbon::now()
                ]);
            }
        }
        
        return redirect()->back();
 

    }

 
    private function getValues($nodes) {

        $nodeValues = $nodes->map(function ($node) {
            if (str_contains($node->nodeValue, '$')) {
                if (preg_match('#^\$ *\d{1,3}(?:,?\d{3})*(?:\.\d\d)?$#', trim($node->nodeValue))) {
                    return $node->nodeValue;
                }
            }
        });

        $matches = $nodeValues->each(function ($node) {
            return $node;
        });

        return $matches->toArray();
    }

    public function delete($id){
        
        $check_exist  = DB::table('urls')->where('id',$id)->first();
        if ($check_exist) {
            DB::table('urls')->where('id',$id)->delete();
            $ck = DB::table('urlattributes')->where('urlid',$check_exist->id)->exists();
            if ($ck) {
                DB::table('urlattributes')->where('urlid',$check_exist->id)->delete();
            }
        }

        return back();
    }


}
